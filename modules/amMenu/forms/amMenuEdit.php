<?php
class MenuEditForm extends Form {
	var $id, $item;

	function __construct() {
		parent::__construct();
		$this->id = Url::getParamInt('id', 0);
		if(Url::getParamAdmin('action','') == 'del'){
			$this->removeMenu();
		}
		$this->item = Menu::getMenuItem($this->id);

		$this->link_js_me('admin_menu.js', __FILE__);
	}
	
	function draw(){
		$data = array();
		Menu::autoEdit($this, $data, 'draw');
    }

    function on_submit(){
		$menu = array();
		if(Menu::autoEdit($this, $menu, 'submit')){
			$menu['id']	= ($this->id <= 0) ? TIME_NOW : $this->id;
			if($menu['id'] != TIME_NOW){
				$menuArr = Menu::getMenuItem($menu['id'],true);
				if(isset($menuArr['items'])){
					$menu['items'] = $menuArr['items'];
				}
			}
			if(Menu::addMenu($menu)){
				Url::redirect('admin', array('cmd' => 'menu'));
			}
			$this->setFormError("","Không lưu được menu");
		}
    }
	
	function removeMenu(){
		if(Menu::removeMenu($this->id)){
			Url::redirect('admin', array('cmd' => 'menu'));
		}
		$this->setFormError("", "Không xóa được menu ".$this->id);
	}
}