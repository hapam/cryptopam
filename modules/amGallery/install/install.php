<?php

function amGallery_install($module_name = ''){
	//lay thong tin module
	$module = DB::fetch("SELECT * FROM ".T_MODULE." WHERE name = '$module_name'");

	//gan vao page quan tri luon
	$page_id  = CGlobal::$corePages['admin'];
	$position = DB::fetch("SELECT max(position) as p FROM ".T_BLOCK." WHERE page_id = $page_id");
	DB::insert(T_BLOCK, array(
		'module_id' => $module['id'],
		'page_id'	=> $page_id,
		'region'	=> 'main',
		'position'  => intval($position['p'])+1,
		'mobile'	=> 0
	));
	DB::update(T_MODULE, array('assign' => 1), 'id='.$module['id']);
}

function amGallery_uninstall($module_name = ''){
	//go bo bang
	DB::query("DROP TABLE IF EXISTS `".T_GALLERY."`");
	DB::query("DROP TABLE IF EXISTS `".T_GALLERY_CATS."`");
}