<?php
class FunctionLib{
	static $check_uri = false;

/*--------------------------------------------------------------------------*/
/* 							PARSE SELECT OPTION								*/
/*--------------------------------------------------------------------------*/
	static function getOption($options_array,$selected){
		$input='';
		if ($options_array)
		foreach($options_array as $key=>$text){
			$input .= '<option value="'.$key.'"';
			if($key==='' && $selected==='')
			{
				$input .=  ' selected';
			}
			else
			if( $selected!=='' && $key==$selected )
			{
				$input .=  ' selected';
			}
			$input .= '>'.$text.'</option>';
		}
		return $input;
	}
	
	static function getOptionHasIdTitle($options_array,$selected = 0){
		$input='';
		if ($options_array)
		foreach($options_array as $key=>$data){
			$input .= '<option value="'.$key.'"';
			if($key==='' && $selected==='')
			{
				$input .=  ' selected';
			}
			else
			if( $selected!=='' && $key==$selected )
			{
				$input .=  ' selected';
			}
			$input .= '>'.$data['title'].'</option>';
		}
		return $input;
	}

	static function getOptionMulti($options,$select_array){
		if ($options)
		foreach($options as $key=>$text){
			$input .= '<option value="'.$key.'"';
			if(in_array($key,$select_array))
			{
				$input .=  ' selected';
			}

			$input .= '>'.$text.'</option>';
		}
		return $input;
	}

	static function getOptionNum($min,$max,$default=1){
		$options = '';
		for($i=$min;$i<=$max;$i++){
			$options .= '<option value="';
			if ( $i<10 )
			$options .= '0'.$i.'"';
			else
			$options .= $i.'"';
			if ( $i == $default )
			{
				$options .= ' selected';
			}
			$options .= '>'.$i.'</option>';
		}
		return $options;
	}
	
	static function getOptionCity($default = 0, $is_active_city = false){
		$groupActive = $groupNormal = $options = '';
		$data = $is_active_city ? CGlobal::$province_active : CGlobal::$province;
		foreach($data as $i => $province){
			$tmp = '<option value="'.$i.'"'.($i==$default?' selected':'').'>'.$province['title'].'</option>';
			if($is_active_city){
				$options .= $tmp;
			}else{
				if($province['status'] == 1){
					$groupActive .= $tmp;
				}else{
					$groupNormal .= $tmp;
				}
			}
		}
		if(!$is_active_city){
			$options = '';
			if($groupActive != ''){
				$options .= '<optgroup label="--- Thành phố ---">'.$groupActive.'</optgroup>';
			}
			if($groupNormal != ''){
				if($groupActive != ''){
					$options .= '<optgroup label="-----  Tỉnh  -----">';
				}
				$options .= $groupNormal;
				if($groupActive != ''){
					$options .= '</optgroup>';
				}
			}
		}
		return $options;
	}

/*--------------------------------------------------------------------------*/
/* 							CHEKING FUNTION									*/
/*--------------------------------------------------------------------------*/

	static function ip_first($ips) {
		if (($pos = strpos($ips, ',')) != false) {
			return substr($ips, 0, $pos);
		}
		return $ips;
	}

	static function ip_valid($ips) {
		if (isset($ips)) {
			$ip    = self::ip_first($ips);
			$ipnum = ip2long($ip);
			if ($ipnum !== -1 && $ipnum !== false && (long2ip($ipnum) === $ip)) { // PHP 4 and PHP 5
				if (($ipnum < 167772160   || $ipnum >   184549375) && // Not in 10.0.0.0/8
					($ipnum < -1408237568 || $ipnum > -1407188993) && // Not in 172.16.0.0/12
					($ipnum < -1062731776 || $ipnum > -1062666241))   // Not in 192.168.0.0/16
				return true;
			}
		}
		return false;
	}

	static function ip() {
		$check = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
	                 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
	                 'HTTP_VIA', 'HTTP_X_COMING_FROM', 'HTTP_COMING_FROM');
		foreach ($check as $c) {
			if (isset($_SERVER[$c]) && self::ip_valid($_SERVER[$c])) {
				return self::ip_first($_SERVER[$c]);
			}
		}
		return $_SERVER['REMOTE_ADDR'];
	}
	
	/*
     * Chặn IP co trong black list
     */
	static function isBlackIP(){
		if(!Url::isAdminUrl()){
			$new_line = array("\r\n", "\n", "\r");
			$ips = str_replace($new_line,',',CGlobal::$black_ips).',';
			return stripos($ips, self::ip().',') !== false;
		}
	}
	
	static function  check_uri(&$query_string = ''){
		if(!self::$check_uri){
			$uri = $_SERVER['REQUEST_URI'];
			
			$query_string = $_SERVER['QUERY_STRING'] ? ('?'.$_SERVER['QUERY_STRING']) : '';
			$dir = (dirname($_SERVER['SCRIPT_NAME'])?dirname($_SERVER['SCRIPT_NAME']):'');
			$dir = str_replace('\\','/', $dir);
			if($dir && $dir != '/' && $dir != './'){
				if($dir[0] != '/'){
					$dir = '/'.$dir;
				}
				$dir .= ($dir[strlen($dir)-1] != '/' ? '/' : '');
				$query_string = str_replace($dir,'',$uri);
			}
			else{
				if(strlen($uri)>1){
					while($uri[0] == '/'){
						$uri = substr($uri,1,strlen($uri)-1);
					}
					$query_string = $uri;
					unset($uri);
				}
				else{
					$query_string = '';
				}
			}
			self::$check_uri = true;
		}
	}
	
	static function is_valid_email($email = '') {
		return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", strtolower(trim($email)));
	}
	
	static function is_mobile($value = ''){
		return preg_match('#^(01([0-9]{2})|09[0-9])(\d{7})$#', $value);
	}
	
	static function isUrlString($str = ''){
		return (bool)preg_match("#^[a-zA-Z][0-9-_a-zA-Z]*$#", $str);
	}
	
	/**
	 * Kiem tra xem mat khau co phai la mat khau qua don gian, de bi nguoi khac doan khong<br />
	 * Vi du: 123456, abcdef la cac mat khau khong an toan
	 * @param string $password
	 * @return boolean TRUE - Neu mat khau khong an toan
	 */
	static function isNotSafePassword($password) {
		$listNotSafePassword = 'abc123,abc@123,123456,qwerty,qazwsx,1234567,12345678,123456789,1234567890,0123456789,654321,0123456,123abc,abcdef,qwertyuiop,1qaz2wsx,password,111111,iloveyou,123123,ashley,bailey,baseball,dragon,football,letmein,master,michael,monkey,passw0rd,shadow,sunshine,superman,trustno1';
		$isNotSafe = false;
		if (stripos(',' . $listNotSafePassword . ',', ','.$password.',') !== false) {
			$isNotSafe = true;
		}
		return $isNotSafe;
	}
	
	static function validCaptcha($str = '', &$error = 0){
		if($str == '' || !isset($_SESSION['captcha_validate'])){
			$error = 1;
			return false;
		}
		$error_time = 4;//so lan cho phep nhap
		if($_SESSION['captcha_validate']['error'] >= $error_time){
			$error = 2;
			return false;
		}
		$time_valid = 120;//captcha song trong 2 phut
		$time = time();
		if($time-$_SESSION['captcha_validate']['time']>$time_valid){
			$error = 3;
			return false;
		}
		if(strtoupper($str) != $_SESSION['captcha_validate']['txt']){
			$error = 4;
			$_SESSION['captcha_validate']['error']++;
			return false;
		}
		return true;
	}

/*--------------------------------------------------------------------------*/
/* 							FORMATER FUNCTIONS								*/
/*--------------------------------------------------------------------------*/
	
	static function JsonErr($msg = 'error', $mixed = array(), $exit = false){
		switch($msg){
			case 'access_denied': $msg = 'Lỗi!!! Không có quyền thực hiện thao tác'; break;
		}
		$arr = array('err' => -1, 'msg' => $msg);
		if(!empty($mixed)){
			$arr = $arr + $mixed;
		}
		if($exit){
			echo json_encode($arr);	exit();
		}
		return json_encode($arr);
	}
	static function JsonSuccess($msg = 'success', $mixed = array(), $exit = false){
		$arr = array('err' => 0, 'msg' => $msg);
		if(!empty($mixed)){
			$arr = $arr + $mixed;
		}
		if($exit){
			echo json_encode($arr);	exit();
		}
		return json_encode($arr);
	}
	
	static function numberFormat($number = 0){
		if($number >= 1000){
			return number_format($number,0,',','.');
		}
		return $number;
	}
	static function priceFormat($price = 0,$currency = ''){
		if($currency == ''){
			$currency = CGlobal::$currency;
		}
		return self::numberFormat($price)." $currency";
	}
	static function dateFormat($time = TIME_NOW, $format = 'd/m - H:i', $vietnam = false){
		$return = date($format,$time);
		if ($vietnam){
			$days = array('Mon' => 'Thứ 2', 'Tue' => 'Thứ 3', 'Wed' => 'Thứ 4', 'Thu' => 'Thứ 5', 'Fri' => 'Thứ 6', 'Sat' => 'Thứ 7', 'Sun' => 'Chủ nhật');
			$return = date('H:i - ',$time).$days[date('D',$time)].', ngày '.date('d/m/Y',$time);
		}
		return $return;
	}

	//duration time
	static function duration_time($time){
		$time = TIME_NOW - $time;

		if($time>0){
			if($time>(365*86400)){
				return floor($time/(365*86400)).' năm trước';
			}

			if($time>(30*86400)){
				return floor($time/(30*86400)).' tháng trước';
			}

			if($time>(7*86400)){
				return floor($time/(7*86400)).' tuần trước';
			}
			if($time>86400){
				return floor($time/(86400)).' ngày trước';
			}

			if($time>3600){
				return floor($time/(3600)).' giờ trước';
			}

			if($time>60){
				return floor($time/(60)).' phút trước';
			}
		}
		return ' vài giây trước';
	}

/*--------------------------------------------------------------------------*/
/* 							Breadcrumb										*/
/*--------------------------------------------------------------------------*/	

	static function addBreadcrumb($title = '', $link = '', $def = true){
		if($title == '' && $link == ''){
			$key = CGlobal::$defaultHomePage;
			$title = 'Trang chủ';
			$link = WEB_ROOT;
		}elseif(empty(CGlobal::$breadcrumb) && $def){
			self::addBreadcrumb();
		}
		$key = md5($link);
		CGlobal::$breadcrumb[$key] = array(
			'title' =>	$title,
			'link'	=>	$link
		);
	}
	
	static function getBreadcrumb(){
		$breadcrumb = '';
		if(!empty(CGlobal::$breadcrumb)){
			$max = count(CGlobal::$breadcrumb);
			if($max > 1){
				$breadcrumb = '<div class="breadcrumb">';
				$counter = 1;
				foreach(CGlobal::$breadcrumb as $item){
					if($counter < $max){
						$breadcrumb .= '<a href="'.$item['link'].'">'.$item['title'].'</a>';
						$breadcrumb .= ' <span class="navigation-pipe"> &gt; </span>';
						if($counter == 1){
							$breadcrumb .= '<span class="navigation_end">';
						}
					}else{
						if($max == 1){
							$breadcrumb .= '<span class="navigation_end">';
						}
						$breadcrumb .= $item['title'];
						if($max >= 1){
							$breadcrumb .= '</span>';
						}
					}
					$counter ++;
				}
				$breadcrumb .= '</div>';
			}
		}
		return $breadcrumb;
	}
	
	static function getAdminBreadcrumb(){
		$breadcrumb = '';
		if(!empty(CGlobal::$breadcrumb)){
			$max = count(CGlobal::$breadcrumb);
			if($max > 1){
				$breadcrumb = '<ol class="breadcrumb breadcrumb-col-cyan">';
				$counter = 1;
				foreach(CGlobal::$breadcrumb as $item){
					if($max==$counter){
						$breadcrumb .= '<li class="active">'.$item['title'].'</li>';
					}else{
						$breadcrumb .= '<li><a href="'.($item['link']!=''?$item['link']:'javascript:void(0)').'">'.$item['title'].'</a></li>';
					}
					$counter++;
				}
				$breadcrumb .= '</ol>';
			}
		}
		return $breadcrumb;
	}
	
/*--------------------------------------------------------------------------*/
/* 							Facebook META DATA								*/
/*--------------------------------------------------------------------------*/

	static function facebookMetaDataSet($arr){
		global $facebook_meta;
		$facebook_meta = $arr;
	}

	static function facebookMetaData($return = false){
        $data  = '';
        if(!Url::isAdminUrl()) {
            global $facebook_meta;
			$title = CGlobal::$website_title;
            $link  = Url::build(CGlobal::$current_page, CGlobal::$urlArgs);
            $image = CGlobal::$logo;
            $des   = CGlobal::$meta_desc;
			if(!empty($facebook_meta)){
				if(isset($facebook_meta['title'])){
					$title = $facebook_meta['title'];
				}
				if(isset($facebook_meta['link'])){
					$link = $facebook_meta['link'];
				}
				if(isset($facebook_meta['image'])){
					$image = $facebook_meta['image'];
				}
				if(isset($facebook_meta['des'])){
					$des = $facebook_meta['des'];
				}
			}

            $data  = '
    <meta property="og:title" content="' . addslashes(htmlspecialchars($title)) . '" />
    <meta property="og:locale" content="vi_VN" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="' . $link . '" />
    <meta property="og:image" content="' . $image . '" />
    <meta property="og:site_name" content="' . CGlobal::$site_name . '" />
    <meta property="og:description" content="' . StringLib::descriptionText($des) . '" />';
        }
        if($return){
            return $data;
        }
        echo $data;
	}
	
/*--------------------------------------------------------------------------*/
/* 							SOME EXTRA FUNCTION								*/
/*--------------------------------------------------------------------------*/
	static function session_started(){
		return isset($_SESSION);
	}
	
	static function empty_all_dir($name, $remove_sub_dir = false,$remove_self=false){
		if(is_dir($name)){
			if($dir = opendir($name)){
				$dirs = array();
				while($file=readdir($dir)){
					if($file!='..' and $file!='.'){
						if(is_dir($name.'/'.$file)){
							$dirs[]=$file;
						}
						else{
							@unlink($name.'/'.$file);
						}
					}
				}
				closedir($dir);
				foreach($dirs as $dir_){
					self::empty_all_dir($name.'/'.$dir_, ($remove_self || $remove_sub_dir),($remove_self || $remove_sub_dir));
				}
				if($remove_self){
					@rmdir($name);
				}
			}
		}
	}
	
	//hidden uid
	static function hiddenID($id = 0, $decode = false){
		if($decode){
			$id = (1984 - 13*12) + $id;
		}else{
			$id = (13*12 - 1984) + $id;
		}
		return $id;
	}

	static function addCondition($condition = array(),$where = false){
    	if(!empty($condition)){
    		$numCondition = count($condition);
    		$newCondition = array();
    		foreach ($condition as $k=>$c){
    			if(strpos($c,' = ') > 0){
    				$newCondition[$k] = $c;
    			}
    			else{
    				$newCondition[$numCondition] = $c;
    				$numCondition ++;
    			}
    		}
    		$c = implode(' AND ', $newCondition);
    		if($where && $c){
    			$c = ' WHERE '.$c;
    		}
    		return $c;
    	}
    	return '';
    }
	
	//background website
	static function getBodyBG(){
		$page = CGlobal::$current_page;
		if($page != 'edit_page' && $page != 'admin' && $page != 'page' && $page != 'module'){
			$bg = CGlobal::$background;
			if($bg != ''){
				echo ' style="background-image:url('.$bg.')"';
			}
		}
	}
	
	static function getBodyClass(){
		$page = CGlobal::$current_page;
		if($page == 'admin_login'){
			return "login-page";
		}elseif($page != 'edit_page'){
			return "theme-".CookieLib::get_cookie('theme-admin', 'cyan');
		}
		return "";
	}
	
	static function mouse_hover($color='#EAF1FB',$return=false){
		$str= ' onmouseover="shop.hover.over(this,\''.$color.'\')" onmouseout="shop.hover.out(this)" ';
		if($return)return $str;else echo $str;
	}
	
	static function readExcel($objPHPExcel){
		$rows = array();
		if(!empty($objPHPExcel)){
			$allData = $objPHPExcel->getActiveSheet()->getCellCollection();
			foreach ($allData as $a){
				$cell = $objPHPExcel->getActiveSheet()->getCell($a);
				$rows[$cell->getRow()][$cell->getColumn()] = $cell->getValue();
			}
		}
		return $rows;
	}
	
	static function tokenData(){
		if(CGlobal::$tokenData == ''){
			CGlobal::$tokenData = md5(session_id().TOKEN_KEY_SECRET);
		}
		return CGlobal::$tokenData;
	}
	
	static function trimID($strID, $character = ',') {
		$strID = trim($strID, $character);
		$strID = preg_replace("/$character+/", $character, $strID);
		$strID = preg_replace("/[^0123456789,]/", '', $strID);
		$arrID = explode($character, $strID);
		foreach($arrID as $k => $v){
			$v = (int)$v;
			if(trim($v) == '' || $v == 0) unset($arrID[$k]);
		}
		$strID = implode(",", array_unique($arrID));
		return $strID;
	}
	
	static function getPathThemes($web = true, $not_check_mobile = false){
		$theme_name = CGlobal::$configs['themes'];
		$dir = 'website';

		if(!$not_check_mobile && CGlobal::$mobile[0] && (CGlobal::$configs['themes_mobile'] != 'no_mobile')){
			$dir = 'mobile';
			$theme_name = CGlobal::$configs['themes_mobile'];
		}
		return ($web ? WEB_THEMES : DIR_THEMES).$dir.'/'.$theme_name.'/';
	}
	
	static function getGA($return = false){
		if(!Url::isAdminUrl()){
			$ga = ConfigSite::getConfigFromDB('ga', '', false, 'site_configs');
			if($ga != ''){
				$ga = StringLib::post_db_parse_html($ga);
				if($return){
					return $ga;
				}
				echo $ga;
			}
		}
	}
	
    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    static function get_gravatar( $email = '', $s = 80, $d = 'identicon', $r = 'g', $img = false, $atts = array() ) {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
}