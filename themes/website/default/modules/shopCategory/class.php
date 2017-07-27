<?php
class shopCategory extends Module {
	static function permission(){
		return array(
			"admin category" => "Quản trị danh mục",
			"add category" => "Tạo danh mục",
			"edit category" => "Sửa danh mục",
			"delete category" => "Xóa danh mục"
		);
	}
    function __construct($row){
        Module::Module($row);

        $cmd = Url::getParamAdmin('cmd','');
        if($cmd == 'category'){
            $action = Url::getParamAdmin('action','');
            switch ($action){
                case 'add':
                    if(User::user_access('add category',0,'access_denied')){
                        require_once 'forms/edit.php';
                        $this->add_form(new EditCategoryForm());
                    }
                    break;
                case'edit':
                	if(User::user_access('edit category',0,'access_denied')){
		                require_once 'forms/edit.php';
		                $this->add_form(new EditCategoryForm());
                	}
	                break;
                case 'delete':
                    $id = Url::getParamInt('id',0);
                    if($id){
                    	if(User::user_access('delete category',0,'access_denied')){
	                    	require_once 'forms/edit.php';
	                        $this->add_form(new EditCategoryForm());
                    	}
                    }
                    Url::redirect('admin', array('cmd' => 'category'));
                    break;
                default:
					if(User::user_access('admin category')){
						require_once 'forms/list.php';
						$this->add_form(new CategoryForm());
					}
            }
        }
    }
}