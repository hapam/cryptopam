<?php
class sysPanel extends Module{
	function __construct($row){
		Module::Module($row);
		if(Url::getParamAdmin('cmd') == 'login'){
			Url::redirect_url(Url::build('admin_login'), 301);
		}elseif(User::user_access('access admin page',0,'access_denied')){
			require_once 'forms/Panel.php';
			$this->add_form(new PanelForm());
			
			require_once 'forms/adminLeftMenu.php';
			$this->add_form(new AdminMenuForm());
			
			require_once 'forms/adminLeftMenuInfo.php';
			$this->add_form(new AdminMenuInfoForm());
			
			require_once 'forms/adminMainHeader.php';
			$this->add_form(new adminMainHeaderForm());
		}
	}
}
