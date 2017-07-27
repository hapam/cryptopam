<?php
class sysModule extends Module{
	function __construct($row){
		Module::Module($row);

		if(User::is_root()){
			require_once 'forms/list.php';
			$this->add_form(new ListModuleAdminForm());
		}
		else{
			Url::redirect('access_denied');
		}
	}
}
