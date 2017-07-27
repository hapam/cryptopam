<?php
class sysPages extends Module{
	function __construct($row){
		Module::Module($row);
		if(User::is_root()){
			$cmd = Url::getParamAdmin('cmd');
			switch ($cmd){
				case 'edit':case 'add':case 'copy':
					require_once 'forms/edit.php';
					$this->add_form(new EditPageAdminForm());
					break;
				case 'delete_all_cache':
					Layout::update_all_page();
					Url::redirect('page');
					break;
				case 'refresh':
					$id = Url::getParamInt('id',0);
					if($id > 0){
						Layout::update_page($id);
						if(Url::check('href')){
							Url::redirect_url($_REQUEST['href']);
						}
					}
					Url::redirect_current();
					break;
				case 'delete':
					$id = Url::getParamInt('id',0);
					if($id > 0){
						DB::delete(T_BLOCK, 'page_id='.$id);
						DB::delete_id(T_PAGE, $id);
						FunctionLib::empty_all_dir(DIR_CACHE.'pages',true);
						FunctionLib::empty_all_dir(DIR_CACHE.'modules',true);
					}
					Url::redirect('page');
					break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListPageAdminForm());
					break;
			}
		}
		else{
			Url::redirect('access_denied');
		}
	}
}
