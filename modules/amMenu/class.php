<?php
class amMenu extends Module {
    static function permission(){
		return array("admin menu" => "Thay đổi menu");
	}
    function __construct($row){
        Module::Module($row);

        $cmd = Url::getParamAdmin('cmd','');
        if($cmd == 'menu' && User::user_access('admin menu',0,'access_denied')){
			$action = Url::getParamAdmin('action','');
			switch ($action){
				case 'add':
				case 'edit':
					require_once 'forms/amMenuEdit.php';
					$this->add_form(new MenuEditForm());
					break;
				case 'del':
					require_once 'forms/amMenuEdit.php';
					$this->add_form(new MenuEditForm());
					break;
				default:
					require_once 'forms/amMenu.php';
					$this->add_form(new amMenuForm());
			}
		}
    }
}