<?php

class login_authenForm extends Form {
    var $user, $errorOTP = 3;
    function __construct() {
        CGlobal::$website_title = "Đăng nhập Bước 2";
        $id = CookieLib::get_cookie(md5("id_user_forstep2"));
        $id = FunctionLib::hiddenID($id, true);

        if($id > 0){
            $this->user = DB::fetch("SELECT * FROM " . T_USERS . " WHERE is_active > 0 AND id=".$id);
        }
        if($this->user){
        }else{
            Url::redirect('admin_login');
        }

        //sai 5 lan thi khoa account
        if(isset($_SESSION['error_otp']) && $_SESSION['error_otp'] >= $this->errorOTP){
            unset($_SESSION['error_otp']);
            DB::update(T_USERS,array('is_active' => 0),"id=$id");
            User::getUser($id, true, true);
            Url::redirect('admin_login');
        }
    }

    function draw() {
        global $display;
        $user = $this->user;

        //kiem tra va gui ma QR code
        $check_startQR = '';
        if ($user['secret'] == '' || $user['qrCodeUrl'] == '') {
            $code = Authentication::getQRcode_user();
            $data = array('user' => $user, 'code' => $code);
            if (EmailLib::sendEmailQRCode($user['email'], $data)) {
                DB::update(T_USERS, $code, 'id=' . $user['id']);
                $check_startQR = $user['email'];
            }
        }

        #Add Messages
        $msg = $this->showFormErrorMessages(1);
        if ($msg == '') {
            $msg = $this->showFormSuccesMessages(1);
        }
        $display->add('msg', $msg);
        
		$display->add('base_url', WEB_ROOT);
		$display->add('site_name', CGlobal::$site_name);
        $display->add('check_startQR', $check_startQR);
        $display->add('log2step_time', ConfigSite::getConfigFromDB('log2step_time', 0, false, 'site_configs'));

        $this->beginForm();
        $display->output('login_authen');
        $this->endForm();
    }

    function on_submit() {
        //init GoogleAuth
        $ga = new PHPGangsta_GoogleAuthenticator();
        $token = Url::getParam('token', '');
        
        $user = $this->user;
        if ($user['secret'] != '' && $user['qrCodeUrl'] != '') {
            $checkResult = $ga->verifyCode($user['secret'], $token, 1);    // 1 = 1*30sec clock tolerance
            if ($checkResult) {
                //luu lai thoi gian OTP
                if(Url::getParam('save_login') == 1){
                    Authentication::saveOtpClient( $user['id']);
                }
                
                //tu dong dang nhap
                CookieLib::my_setcookie(md5("password"), $user['password']);
                User::LogIn($user['id'], true);
                Url::redirect('admin');
            }
        }
        if(!isset($_SESSION['error_otp'])){
            $_SESSION['error_otp'] = 1;
        }else{
            $_SESSION['error_otp']++;
        }
        $msg = "Mã OTP sai";
        if($_SESSION['error_otp'] == $this->errorOTP){
            $msg = "Mã OTP sai, account sẽ bị khóa";
        }
        $this->setFormError('', $msg);
    }

}