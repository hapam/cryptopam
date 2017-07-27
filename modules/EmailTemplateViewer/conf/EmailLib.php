<?php

class EmailLib{
	static  $site_title = "Simple Ecomere Core",
            $direct_dir_path = 'EmailTemplateViewer/tpl',
            $logo = array(
                'url' => 'style/images/logo/logo_email.png',
                'width' => 186,
                'height' => 45
            );

	static function sendEmailTest($data = array()){
		global $display;

        $pathTplEmail = self::$direct_dir_path;
        if(Language::$haveToTranslate){
            $pathTplEmail .= '/'.Language::$activeLang;
        }

		if(!empty($data)){
			$display->add('WEB_ROOT', WEB_ROOT);
			$display->add('site_name', CGlobal::$site_name);
			$display->add("support_msg", CGlobal::$messenger_support[22]);
			$display->add("support_city", CGlobal::$province_active[22]);
			$display->add('data', $data);
            $content = $display->output('mailTest', true, $pathTplEmail);

            $email = $data['email'];
            $subject = '[Test] Sent from '.CGlobal::$site_name;
            $img = array(
                array('id'=> 'logo', 'src' => ROOT_PATH.self::$logo['url'], 'title' => self::$site_title)
            );

			return System::send_mail("", $email, $subject, $content, $img);
		}
		return false;
	}
	
	static function sendEmailQRCode($email, $params = array()) {
        global $display;

        $display->add('user', $params['user']);
        $display->add('code', $params['code']);
        
        $display->add('WEB_ROOT', WEB_ROOT);
        $display->add('logo', self::$logo);
        $display->add('site_name', CGlobal::$site_name);
        $display->add("support_msg", CGlobal::$messenger_support[22]);
		$display->add("support_city", CGlobal::$province_active[22]);

        $mailContent = $display->output('email_QRCodeAdmin', true, self::$direct_dir_path);
		$mailSubject = 'Mã QR CODE đăng nhập bước 2 Admin - '.CGlobal::$site_name;
		$img = array(
			array('id'=> 'logo', 'src' => ROOT_PATH.self::$logo['url'], 'title' => self::$site_title)
		);

        return System::send_mail("", $email, $mailSubject, $mailContent, $img);
    }
}
