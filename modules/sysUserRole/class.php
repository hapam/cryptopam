<?php
class sysUserRole extends Module{
	static function permission(){
		return array(
			"admin role" 	 =>	"Quản trị nhóm quyền",
			"add role" 	 =>	"Tạo nhóm quyền",
			"edit role"	 =>	"Sửa nhóm quyền",
			"delete role"=>	"Xóa nhóm quyền",
			"permission" => "Phân quyền cho các nhóm"
		);
	}

	function __construct($row){
		Module::Module($row);

		$cmd = Url::getParamAdmin('cmd','');
		if($cmd == 'user-role'){
			$action = Url::getParamAdmin('action','');
			switch ($action){
				case 'delete':
					if(User::user_access('delete role', 0, 'access_denied')){
						require_once 'forms/ListRole.php';
						$this->add_form(new ListRoleForm());
					}
				break;
				case 'permission':
					if(User::user_access('permission', 0, 'access_denied')){
						require_once 'forms/Permission.php';
						$this->add_form(new PermissionForm());
					}
				break;
				case 'add':
					if(User::user_access('add role', 0, 'access_denied')){
						require_once 'forms/EditRole.php';
						$this->add_form(new EditRoleForm());
					}
				break;
				case 'edit':
					if(User::user_access('edit role', 0, 'access_denied')){
						require_once 'forms/EditRole.php';
						$this->add_form(new EditRoleForm());
					}
				break;
				default:
					if(User::user_access('admin role', 0, 'access_denied')){
						require_once 'forms/ListRole.php';
						$this->add_form(new ListRoleForm());
					}
			}
		}
	}
}
