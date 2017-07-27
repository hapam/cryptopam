<?php

if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_index {
	function playme(){
		$code = Url::getParam('code');
		switch( $code ){
			case 'valid-captcha':
				$this->validCaptcha();
				break;
			case 'home':
			default:
				$this->home();
				break;
		}
	}
	
	function validCaptcha(){
		$str = Url::getParam('str');
		$err = 0;
		if(FunctionLib::validCaptcha($str,$err)){
			FunctionLib::JsonSuccess('ok',false,true);
		}
		FunctionLib::JsonErr($err,false,true);
	}
	
	function home(){die("Nothing to do...");}
}
