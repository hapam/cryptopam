<?php
class sysLogin extends Module{
    function __construct($row){
        Module::Module($row);
        
		//neu sign out thi thoat ra
		if(CGlobal::$current_page == 'sign_out'){
			if(User::is_login()){
				User::LogOut();
			}
			Url::redirect('admin_login');
		}
		if(CGlobal::$current_page == 'admin_login'){
			if(!User::is_login()){
				$log2step = ConfigSite::getConfigFromDB('log2step', 0, false, 'site_configs');
				if($log2step){
					$action = Url::getParamAdmin('cmd', '');
					if($action == 'authenticator'){
						$id = CookieLib::get_cookie(md5("id_user_forstep2"));
						if($id == ''){
							Url::redirect('admin_login');
						}else{
							require_once 'forms/login_authen.php';
							$this->add_form(new login_authenForm());
						}
					}else{
						require_once 'forms/login.php';
						$this->add_form(new amLoginForm());
					}
				}else{
					require_once 'forms/login.php';
					$this->add_form(new amLoginForm());
				}
			}else{
				Url::redirect('admin');
			}
		}
    }
}