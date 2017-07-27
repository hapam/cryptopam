<?php
class amLoginForm extends Form {
	function __construct(){
		$this->link_js("js/jquery/jquery.form.js");
		$this->link_js_me('admin_login.js', __FILE__);
	}
	function draw(){
		global $display;

		$number_error = ConfigSite::getConfigFromDB('captcha_error', 1, false, 'site_configs');
		$errorPass = isset($_SESSION['error_pass_login']) ? $_SESSION['error_pass_login'] : 0;
		$showCaptcha = (CGlobal::$adminCaptcha == 1) && ($errorPass >= $number_error);
		if( $showCaptcha ){
			require_once(ROOT_PATH."includes/recaptcha/recaptchalib.php");
			$error = null;
			$display->add('public_key', CGlobal::$captchaPublic);
			$display->add('recaptcha', recaptcha_get_html(CGlobal::$captchaPublic, $error));
		}

		$display->add('base_url', WEB_ROOT);
		$display->add('site_name', CGlobal::$site_name);
		$display->add('logo', CGlobal::$logo);
		$display->add('captcha', $showCaptcha);
		$display->add('log2step', ConfigSite::getConfigFromDB('log2step', 0, false, 'site_configs'));
		$display->output('login');
	}
}