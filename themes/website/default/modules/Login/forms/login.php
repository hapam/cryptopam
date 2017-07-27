<?php
class LoginForm extends Form{
	function __construct(){
		$this->link_js(FunctionLib::getPathThemes().'javascript/bootstrap.min.js', true);
		
		$this->link_css(FunctionLib::getPathThemes().'style/bootstrap.min.css', true);
		$this->link_css(FunctionLib::getPathThemes().'style/bootstrap-theme.min.css', true);
		
		$this->link_css_me('login.css', __FILE__);
		$this->link_js_me('signup.js', __FILE__);
		
		if(User::is_login()){
			Url::redirect('follow');
		}
	}

	function draw(){
		global $display;

		//config default for all site content
		$display->add('base_url', WEB_ROOT);
		$display->add('site_name', CGlobal::$site_name);
		$display->add('site_title', CGlobal::$website_title);
		$display->add('logo', CGlobal::$logo);
        $display->add('logo_size', CGlobal::$logo_size);
		$display->add('logo_title', CGlobal::$logo_title != '' ? CGlobal::$logo_title : '');
		$display->add('blank_image', 'style/images/blank.gif');

        $this->beginForm();
		$display->output("login");
        $this->endForm();
	}

    function on_submit(){
        $username = trim(Url::getParam('username',''));
        $pass = Url::getParam('pass','');
        $rem = Url::getParam('remember', 0);
		
        if (strlen($username) <3  || strlen($username) >50  || preg_match('/[^A-Za-z0-9_\.@]/',$username) || strlen($pass)<5){
            $this->setFormError("", "Dữ liệu không hợp lệ");
        }
        else
        {
            $user = preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/",$username) ? addslashes($username) : str_replace(array('"','\\'),'_',$username);
            $user_data = DB::fetch("SELECT * FROM ".T_USERS." WHERE username='$user'");
            if($user_data && $user_data['password'] == User::encode_password($pass)){
                if((isset($user_data['block_id']) && intval($user_data['block_id']) > 0) || $user_data['status'] <= 0){
                    $this->setFormError("", "Người dùng bị khóa");
                }elseif(USER_ACTIVE_ON && !$user_data['is_active']){//Chưa kích hoạt
                    $this->setFormError("", "Người dùng chưa kích hoạt");
                }else{
					if($rem==1){
						CookieLib::my_setcookie(md5("password"), $user_data['password']);
					}
					User::LogIn($user_data['id'], true);
					Url::redirect('follow');
                }
            }
            else{
                $this->setFormError("", "Sai mật khẩu");
            }
        }
    }
}

