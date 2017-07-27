<?php
class myCoin extends Module{
	static function permission(){
		return array(
			"admin myCoin"  => "Quản trị",
			"add myCoin"  => "Thêm",
			"edit myCoin"  => "Sửa",
			"delete myCoin"  => "Xóa"
		);
	}
	function __construct($row){
		Module::Module($row);

		if(Url::isAdminUrl()){
			$cmd = Url::getParamAdmin('cmd','');
			if ($cmd == 'crypto' && User::user_access('admin myCoin', 0, 'access_denied')) {
				$action = Url::getParamAdmin('action', '');
				switch ($action) {
					case 'add':
						if (User::user_access('add myCoin', 0, 'access_denied')) {
							require_once 'forms/admin_edit.php';
							$this->add_form(new EditmyCoinForm());
						}
						break;
					case'edit':
						if (User::user_access('edit myCoin', 0, 'access_denied')) {
							require_once 'forms/admin_edit.php';
							$this->add_form(new EditmyCoinForm());
						}
						break;
					case 'delete':
						$id = Url::getParamInt('id', 0);
						if ($id > 0) {
							if (User::user_access('delete myCoin', 0, 'access_denied')) {
								require_once 'forms/admin_edit.php';
								$this->add_form(new EditmyCoinForm());
							}
						}
						Url::redirect('admin', array('cmd' => 'crypto'));
						break;
					default:
						require_once 'forms/admin_list.php';
						$this->add_form(new ListmyCoinForm());
				}
			}
		}else{
			require_once 'forms/myCoin.php';
			$this->add_form(new myCoinForm());
		}
	}
}