<?php
class sysProvince extends Module{
	static function permission(){
		return array(
			"admin province"  => "Quản trị vùng miền",
			"add province"    => "Tạo vùng miền",
			"edit province"   => "Sửa vùng miền",
			"delete province" => "Xóa vùng miền"
		);
	}
    function __construct($row){
        Module::Module($row);

        $cmd = Url::getParamAdmin('cmd', '');
        if($cmd == 'province')
        {
        	$action = Url::getParamAdmin('action', '');
	        switch ($action){
	            case 'add':
					if(User::user_access('add province',0,'access_denied')){
						require_once 'forms/edit.php';
						$this->add_form(new EditProvinceForm());
					}
				break;
				case'edit':
					if(User::user_access('edit province',0,'access_denied')){
						require_once 'forms/edit.php';
						$this->add_form(new EditProvinceForm());
					}
	            break;
	            case 'delete':
	                $id = Url::getParamInt('id',0);
	                if($id > 0 && User::user_access('delete province',0,'access_denied')){
	                    DB::delete(T_PROVINCE, 'id=' . $id);
						$_SESSION['province_update_cached'] = true;
						Url::redirect('admin', array('cmd' => 'province'));
	                }
	            break;
				default:
					if(User::user_access('admin province',0,'access_denied')){
						require_once 'forms/list.php';
						$this->add_form(new ListProvinceForm());
					}
	        }
        }
    }
}