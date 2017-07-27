<?php
class sysStaticPage extends Module {
    static function permission(){
		return array(
			"admin static page" => "Quản trị trang tĩnh",
			"add page" => "Tạo trang tĩnh",
			"edit page" => "Sửa trang tĩnh",
			"del page" => "Xóa trang tĩnh",
		);
	}
    function __construct($row){
        Module::Module($row);

        if(Url::isAdminUrl()){
			if(Url::getParamAdmin('cmd') == 'trang-tinh'){
				switch(Url::getParamAdmin('action')){
					case 'add':
						if(User::user_access('add page',0,'access_denied')){
							require_once 'forms/edit.php';
							$this->add_form(new editStaticPageForm());
						}
					break;
					case 'edit':
						if(User::user_access('edit page',0,'access_denied')){
							require_once 'forms/edit.php';
							$this->add_form(new editStaticPageForm());
						}
					break;
					case 'del':
						if(User::user_access('del page',0,'access_denied')){
							require_once 'forms/edit.php';
							$this->add_form(new editStaticPageForm());
						}
					break;
					default:
						if(User::user_access('admin static page',0,'access_denied')){
							require_once 'forms/list.php';
							$this->add_form(new listStaticPageForm());
						}
				}
			}
		}else{
			require_once 'forms/staticPage.php';
			$this->add_form(new StaticPageForm());
		}
    }
}