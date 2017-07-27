<?php
class amBackup extends Module{
	static function permission(){
		return array(
			"admin backup"  => "Quản trị",
			"restore backup" => "Khôi phục",
			"delete backup"  => "Xóa backup"
		);
	}
	function __construct($row){
		Module::Module($row);
		if(Url::isAdminUrl()){
			$cmd = Url::getParamAdmin('cmd','');
			if ($cmd == 'backup' && User::user_access('admin backup', 0, 'access_denied')) {
				require_once 'forms/admin_list.php';
				$this->add_form(new ListBackupForm());
			}
		}
	}
}