<?php
class sysThemes extends Module{
	function __construct($row){
		Module::Module($row);

		if(User::is_root()){
			require_once 'forms/list.php';
			$this->add_form(new ListThemesForm());
		}
		else{
			Url::redirect('access_denied');
		}
	}
}
