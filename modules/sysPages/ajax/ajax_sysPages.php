<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_sysPages {
	function playme() {
		$code = Url::getParam('code');
		switch( $code ) {
			case 'load-layout':
				$this->loadLayout();
				break;
			default: $this->home();
		}
	}
	function loadLayout() {
		$themes = Url::getParam('themes');
		$mobile = Url::getParam('mobile');
		$admin = Url::getParamInt('admin', 0);
		$dir = DIR_THEMES;
		if($admin == 1){
			$dir = ROOT_PATH;
		}else{
			if($mobile != ''){
				if($mobile == 'no_mobile'){
					$mobile = CGlobal::$configs['themes_mobile'] != 'no_mobile' ? CGlobal::$configs['themes_mobile'] : $mobile;
				}
				if($mobile != 'no_mobile'){
					$dir .= 'mobile/'.$mobile.'/';
				}
			}else{
				$dir .= 'website/'.(($themes != '' && $themes != 'sys') ? $themes : CGlobal::$configs['themes']).'/';
			}
		}
		$layouts = array(''=>'-- Chọn layout --');
		if($dir != DIR_THEMES){
			$dir = opendir($dir.'layouts');
			while($file = readdir($dir)){
				if(($file != '.') && ($file != '..')){
					$layouts['layouts/'.$file] = basename($file,'.'.FileHandler::getExtension($file,'html'));
				}
			}
			closedir($dir);
		}
		FunctionLib::JsonSuccess('done', array('layout' => $layouts), true);
	}
	function home() {
		die("Nothing to do...");
	}
}