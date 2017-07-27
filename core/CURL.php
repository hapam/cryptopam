<?php
//curl
class CURL {
	var $callback = false;

	function setCallback($func_name) {
	    $this->callback = $func_name;
	}

	function doRequest($method, $url, $vars, $opts = array()) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
	    //curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

	    //dungbt them vao de post duoc len lighttpd
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		
		if(!empty($opts)){
			foreach($opts as $opt){
				curl_setopt($ch, $opt['k'], $opt['v']);
			}
		}
		$method = strtolower($method);
	    if ($method == 'post') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }
	    $data = curl_exec($ch);

	    if ($data) {
			curl_close($ch);
	        if ($this->callback)
	        {
	            $callback = $this->callback;
	            $this->callback = false;
	            return call_user_func($callback, $data);
	        } else {
	            return $data;
	        }
	    }

		$err = curl_error($ch);
		curl_close($ch);
		return $err;
	}

	function get($url, $opts = array()) {
	    return $this->doRequest('GET', $url, 'NULL', $opts);
	}

	function post($url, $vars) {
	    return $this->doRequest('POST', $url, $vars);
	}
	function multiRequest($arrUrl = array(),$method = 'get'){
		$results = array();
		if(!empty($arrUrl)){
			$numThread = count($arrUrl);
			$method = strtolower($method);
			$curl_arr = array();
			//create the multiple cURL handle
			$mh = curl_multi_init();
			for($i = 0; $i < $numThread; $i++){
				$curl_arr[$i] = curl_init();
				curl_setopt($curl_arr[$i], CURLOPT_URL, $arrUrl[$i]['url']);
				curl_setopt($curl_arr[$i], CURLOPT_HEADER, 0);
				curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl_arr[$i], CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl_arr[$i], CURLOPT_HTTPHEADER, array('Expect:'));
				
				if($method == 'post'){
					curl_setopt($curl_arr[$i], CURLOPT_POST, 1);
		       		curl_setopt($curl_arr[$i], CURLOPT_POSTFIELDS, $arrUrl[$i]['data']);
				}
				curl_multi_add_handle($mh, $curl_arr[$i]);
			}

			do {
			    curl_multi_exec($mh,$running);
			} while ($running > 0);
			
			
			for($i = 0; $i < $numThread; $i++){
				$results[] = curl_multi_getcontent  ( $curl_arr[$i]  );
			}
			curl_multi_close($mh);
		}
		return $results;
	}
	/**
	 * get multi thread url
	 * @param array $arrUrl mang url 2 chieu dang array(0=>array('url'=>$url))
	 * @return array
	 */
	function multiGet($arrUrl = array()){
		if(!empty($arrUrl)){
			return $this->multiRequest($arrUrl,'get');
		}
	}
	/**
	 * post multi thread url
	 * @param array $arrUrl mang url 2 chieu dang array(0=>array('url'=>$url,'data'=>$dataArray))
	 * @return array
	 */
	function multiPost($arrUrl = array()){
		if(!empty($arrUrl)){
			return $this->multiRequest($arrUrl,'post');
		}
	}
}
