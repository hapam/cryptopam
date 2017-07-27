<?php
class sysUser extends Module{
	static function permission(){
		return array(
			"admin user" 	=>	"Quản trị User",
			"add user" 	=>	"Tạo User",
			"edit user"	=> 	"Sửa User",
			"delete user"=>	"Xóa User",
			"block user" =>	"Khóa User",
			"log user" =>	"Xem Log User"
		);
	}
	function __construct($row){
		Module::Module($row);

		$cmd = Url::getParamAdmin('cmd','');
		if($cmd == 'user'){
			$action = Url::getParamAdmin('action','');
			switch ($action){
				case 'delete':
					if(User::user_access('delete user',0,'access_denied')){
						require_once 'forms/listUser.php';
						$this->add_form(new listUserForm());
					}
				break;
				case 'add':
					if(User::user_access('add user',0,'access_denied')){
						require_once 'forms/EditUser.php';
						$this->add_form(new EditUserForm());
					}
				break;
				case 'edit':
					if(User::user_access('edit user',0,'access_denied')){
						require_once 'forms/EditUser.php';
						$this->add_form(new EditUserForm());
					}
				break;
				default:
					if(User::user_access('admin user',0,'access_denied')){
						require_once 'forms/listUser.php';
						$this->add_form(new listUserForm());
					}
			}
		}
	}
}
