<?php
class amMenuForm extends Form {
	var $key = 'site_menu', $menu = array();

	function __construct() {
		$this->menu = Menu::getMenu();
		$this->menu = Menu::fetchMenu($this->menu);
	}

	function draw(){
        global $display;

		$data = array();
		foreach(Menu::getMenuType() as $k => $title){
			$data[] = array(
				't' => $title,
				'items' => Menu::getMenu($k)
			);
		}

		$msg = $this->showFormErrorMessages(1);
		if($msg == ''){
			$msg = $this->showFormSuccesMessages(1);
		}

		$display->add('msg', $msg);
		$display->add('all_menu', $data);
		$display->add('addUrl', Url::buildAdminURL('admin',array('cmd' => 'menu', 'action' => 'add')));
		$display->add('editURL', Url::buildAdminURL('admin',array('cmd' => 'menu', 'action' => 'edit')));
		$display->add('delURL', Url::buildAdminURL('admin',array('cmd' => 'menu', 'action' => 'del')));

		$display->output("list");
    }
}