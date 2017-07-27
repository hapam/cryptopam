<?php
class Login extends Module{
	function __construct($row){
		Module::Module($row);
		
		$signout = Url::getParamInt('signout', 0);
		
		if($signout == 1){
			if(User::is_login()){
				User::LogOut();
			}
			Url::redirect('login');
		}else{
			require_once 'forms/login.php';
			$this->add_form(new LoginForm);
		}
	}
}
