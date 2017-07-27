<?php
class sysAdminPanel extends Module{
    function __construct($row){
        Module::Module($row);
		
        if(CGlobal::$current_page == 'admin' && Url::getParamAdmin('cmd') == '' && User::user_access('access admin page',0,'access_denied')){
			require_once 'forms/AdminPanel.php';
			$this->add_form(new AdminPanelForm());
        }
    }
}