<?php
class sysModuleAuto extends Module{
	static function permission(){
		return array(
			"admin module writer"  => "Sinh code",
		);
	}
	function __construct($row){
		Module::Module($row);

		$cmd = Url::getParamAdmin('cmd','');
		if($cmd == 'code'){
			if(User::user_access('admin module writer',0,'access_denied')){
				require_once 'forms/list.php';
				$this->add_form(new sysModuleAutoForm());
			}
		}
	}
}

