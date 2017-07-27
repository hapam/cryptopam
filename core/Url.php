<?php
class Url{
	static function fetchUrlArg(){
		//lay cac bien ve
		$q = urldecode(REWRITE_ON ? $_SERVER['REQUEST_URI'] : $_SERVER['QUERY_STRING']);
		$querystring = parse_url($q, PHP_URL_QUERY);
		parse_str($querystring, CGlobal::$urlVars);

		//lay cac tham so URL
		$router = '';
		if(isset($_REQUEST['q'])){
			$router = $_REQUEST['q'];
		}
		if($router != ''){
			if(!REWRITE_ON){
				$router = explode('.html', $router);
				$router = $router[0];
			}
			CGlobal::$urlArgs = explode('/', $router);
		}
		if(empty(CGlobal::$urlArgs)){
			CGlobal::$urlArgs = array(CGlobal::$defaultHomePage);
		}
		return self::getPageName();
	}
	
	static function getPageName(){
		$page_name = CGlobal::$urlArgs[0];
		if(!isset(CGlobal::$arrPage[$page_name])){//ko tim thay page
			//kiem tra trang tinh truoc
			$check = explode('trang-', $page_name);
			if(count($check) > 1 && $check[0] == ''){
				$page_name = 'trang_tinh';
				CGlobal::$urlVars['url'] = $check[1];
			}elseif(REWRITE_ON){
				$found = false;
				foreach(CGlobal::$arrPage as $p){
					if($p['rewrite'] == $page_name){
						$page_name = $p['name'];
						$found = true;
						break;
					}
				}
				//chuyen sang trang bao loi ko ton tai trang
				if(!$found){
					self::not_found();
				}
			}else{
				//chuyen sang trang bao loi ko ton tai trang
				self::not_found();
			}
		}

		//start kiem tra arg

			//neu la page dac biet thi kiem tra o day va tra ve dung page name trong mang CGlobal::$arrPage
			
			//check danh muc & san pham
//			if(CGlobal::$urlArgs[0] == 'san-pham' || $page_name == 'product' || $page_name == 'category'){
//				$category = Category::getCatInfoByName(CGlobal::$urlArgs[1]);
//				if(!empty($category)){
//					CGlobal::$urlVars['id'] = $category['id'];
//					CGlobal::$urlVars['parent_id'] = $category['parent_id'];
//				}else{//chuyen sang trang san pham
//					$page_name = 'product';
//				}
//			}

		//end kiem tra

		return strtolower($page_name);
	}
	
/*--------------------------------------------------------------------------*/
/* 							Create URL 										*/
/*--------------------------------------------------------------------------*/

    static function buildURL($page, $params=array(), $anchor=''){
        $request_string = '';
        if($page!=CGlobal::$defaultHomePage && isset(CGlobal::$arrPage[$page])){
            $router = '';
            if(!empty($params)){
                $router = implode('/', $params);
            }
            if(REWRITE_ON){
                $request_string = (CGlobal::$arrPage[$page]['rewrite'] != '') ? CGlobal::$arrPage[$page]['rewrite'] : $page;
            }else{
                $request_string = '?q='.$page;
            }
            $request_string .= (($router != '') ? ('/'.$router) : '').'.html';
        }
        return WEB_ROOT.$request_string.$anchor;
    }

    static function build($page,$params=array(),$anchor=''){
        return self::buildURL($page, $params, $anchor);
    }

    static function buildAdminURL($page,$params=array(),$anchor=''){
        $mainParams = array();
        $query_string = '';
        foreach($params as $k => $v){
            if($k == 'cmd' || $k == 'action'){
                $mainParams[] = $v;
                unset($params[$k]);
            }else{
//                $query_string .= ($query_string != '' ? '&' : '')."$k=$v";
                if(!is_array($v)){
                    $query_string .= ($query_string != '' ? '&' : '')."$k=$v";
                }
                else{
                    foreach ($v as $vv){
                        $query_string .= ($query_string != '' ? '&' : '')."$k%5B%5D=$vv";
                    }
                }
            }
        }
        if($anchor != ''){
            $anchor .= $query_string;
        }elseif ($query_string != ''){
            $anchor = '?'.$query_string;
        }
        return self::buildURL($page, $mainParams, $anchor);
    }

    static function build_current($params=array(),$anchor=''){
        $page_name = (isset(Layout::$page['name'])) ? Layout::$page['name'] : CGlobal::$defaultHomePage;
        return Url::buildAdminURL($page_name, $params, $anchor);
    }
	
/*--------------------------------------------------------------------------*/
/* 							Get Param From URL 								*/
/*--------------------------------------------------------------------------*/

	static function getParam($aVarName = "", $aVarAlt = "", $method = "") { //$method = $_POST $_GET
        if($method == 'POST' && isset($_POST[$aVarName])){
			$lVarName = $_POST[$aVarName];
		}else{
            if (isset(CGlobal::$urlVars[$aVarName])) {
                $lVarName = CGlobal::$urlVars[$aVarName];
            } elseif (isset($_GET[$aVarName])) {
                $lVarName = $_GET[$aVarName];
            } elseif (isset($_POST[$aVarName])) {
                $lVarName = $_POST[$aVarName];
            } else {
                $lVarName = $aVarAlt;
            }
        }
        if ($lVarName != $aVarAlt) {
            if (is_array($lVarName)) {
                $lReturnArray = array();
                foreach ($lVarName as $key => $value) {
                    $value = StringLib::clean_value($value);
                    $key = StringLib::clean_key($key);
                    $lReturnArray[$key] = $value;
                }
                return $lReturnArray;
            } else {
                return StringLib::clean_value($lVarName); // Clean input and return it
            }
        }
        return $lVarName;
    }

	static function getParamInt($aVarName,$aVarAlt=0, $method = ""){
		if($method == 'POST' && isset($_POST[$aVarName])){
			$lNum = $_POST[$aVarName];
		}else{
			if(isset(CGlobal::$urlVars[$aVarName])){
				$lNum =  CGlobal::$urlVars[$aVarName];
			}elseif (isset($_POST[$aVarName])){
				$lNum = $_POST[$aVarName];
			}elseif (isset($_GET[$aVarName])){
				$lNum = $_GET[$aVarName];
			}else{
				$lNum = $aVarAlt;
			}
		}
		if($lNum == ""){
			return (int)$aVarAlt;
		}
		return (int)$lNum;
	}
	
	static function getParamAdmin($key = ''){
		if($key == 'cmd' || $key == 'action'){
			$idx = ($key == 'cmd') ? 1 : 2;
			if(isset(CGlobal::$urlArgs[$idx])){
				return CGlobal::$urlArgs[$idx];
			}
		}
		return self::getParam($key);
	}

/*--------------------------------------------------------------------------*/
/* 							Another Function 								*/
/*--------------------------------------------------------------------------*/

	static function check($params){
		if(!is_array($params)){
			$params = array(0 => $params);
		}
		foreach($params as $param=>$value){
			if(is_numeric($param)){
				return isset($_REQUEST[$value]);
			}
			else{
				return isset($_REQUEST[$value]) && ($_REQUEST[$param] == $value);
			}
		}
		return true;
	}

	//Chuyen sang trang chi ra voi $url
	static function redirect($page='',$params=array(), $anchor=''){
		$url = '';
		if($page != ''){
			$url = Url::isAdminUrl($page) ? Url::buildAdminURL($page, $params, $anchor) : Url::build($page, $params, $anchor);
		}
		Url::redirect_url($url);
	}
	
	static function redirect_current($params=array(),$anchor = ''){
		if(empty($params)){
			$params = CGlobal::$urlVars;
		}
		Url::redirect(CGlobal::$current_page,$params,$anchor);
	}

	static function redirect_url($url='', $type=0){
		if($url != '' && strpos($url,WEB_ROOT) === 0){
			$url = substr($url,strlen(WEB_ROOT));
		}
		if($type == 301){
			Header( "HTTP/1.1 301 Moved Permanently" );
		}
		header('Location:'.WEB_ROOT.$url);
		System::halt();
	}
	
	static function goback(){
		header('Location:'.$_SERVER['HTTP_REFERER']);
        System::halt();
	}
	
	static function access_denied(){
		if(Url::isAdminUrl(CGlobal::$current_page) && !User::is_login()){
			Url::redirect('admin', array('cmd' => 'login'));
		}
		Url::redirect('access_denied');
	}
	
	static function not_found(){
		Url::redirect('error');
	}

	static function isAdminUrl($page = ''){
		$arrAdminUrl = array('edit_page' => 1, 'admin' => 1, 'page' => 1, 'module' => 1, 'themes' => 1, 'admin_login' => 1);
		$page = $page != '' ? $page : CGlobal::$current_page;
		return isset($arrAdminUrl[$page]);
	}
	
	static function isCurServer($nameCheck = 'localhost'){
		$curSer = $_SERVER ['SERVER_ADDR'];
		return ($curSer == 'localhost') || ($curSer == '127.0.0.1') || (stripos($curSer, $nameCheck) !== false);
	}
	
	static function downloadLink($name = '', $dir = '', $time = 0){
		$dir = str_replace('/', '', $dir);
		return WEB_ROOT . 'tools/download.php?f='.$name.'&d='.$dir.'&t='.$time;
	}
}
