<?php
class sysLanguage extends Module{
	static function permission(){
		return array(
			"admin lang"  => "Quản trị Ngôn ngữ",
			"add lang"    => "Thêm bản dịch ngôn ngữ",
			"edit lang"   => "Sửa bản dịch ngôn ngữ",
			"delete lang" => "Xóa bản dịch ngôn ngữ",
			"add other lang"    => "Thêm ngôn ngữ",
		);
	}
    function __construct($row){
        Module::Module($row);
        $cmd = Url::getParamAdmin('cmd');
        if($cmd == 'lang'){
        	$action = Url::getParamAdmin('action');
	        switch ($action){
	            case 'add':
					if(User::user_access('add lang',0,'access_denied')){
						require_once 'forms/edit.php';
						$this->add_form(new EditLangForm());
					}
				break;
				case'edit':
					if(User::user_access('edit lang',0,'access_denied')){
						require_once 'forms/edit.php';
						$this->add_form(new EditLangForm());
					}
	            break;
	            case 'delete':
	                $id = Url::getParamInt('id',0);
	                if($id > 0 && User::user_access('delete lang',0,'access_denied')){
						DB::delete(T_LANG, "id = $id OR pid = $id");
						//chuyen trang
						//Url::redirect('admin', array('cmd' => 'lang'));
						Url::goback();
	                }
	            break;
				default:
					if(User::user_access('admin lang',0,'access_denied')){
						require_once 'forms/list.php';
						$this->add_form(new AdminLangForm());
					}
	        }
        }
    }
}