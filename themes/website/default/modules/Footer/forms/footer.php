<?php
class FooterForm extends Form{
	function __construct(){}

	function draw(){
		global $display;
		$page = CGlobal::$current_page;
		$menu = Menu::getMenu(2);
		foreach($menu as $k => $v){
			if($menu[$k]['parent'] == 0 && ($v['link'] == $page || stripos($v['link'],$page.'/') !== false || stripos($v['link'],$page.'.html') !== false)){
				$menu[$k]['active'] = true;break;
			}
		}
		$display->add('footer_menu', $menu);
		$display->output('Footer');
	}
}
