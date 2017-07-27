<?php
class CookieLib{
/*--------------------------------------------------------------------------*/
/* 							Xu Ly COOKIE									*/
/*--------------------------------------------------------------------------*/

	static function my_setcookie($name="", $value = "", $expires=""){
		$name = COOKIE_ID."_".$name;
		$expires = ($expires)? $expires : time() + 86400*365;
		$cookie_path = '/';
		$cookie_domain = "";//DOMAIN;
		if(preg_match(DOMAIN_COOKIE_REG_STRING, $_SERVER['HTTP_HOST'])){
			$cookie_domain = DOMAIN_COOKIE_STRING;
		}
		setcookie($name, $value, $expires, $cookie_path, $cookie_domain);
		$_COOKIE[$name] = $value;
	}

	static function get_cookie($name="", $def = ""){
		$name = COOKIE_ID."_".$name;
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $def;
	}
	
	static function isCookieExisted($name=""){
		return isset($_COOKIE[COOKIE_ID."_".$name]);
	}
}
