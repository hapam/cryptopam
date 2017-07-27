<?php
function {$mod_name}_install($module_name = ''){literal}{{/literal}
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
	
	//gan vao menu quan tri
	$pid = 1489487652;
	if(!Menu::getMenuItem($pid)){literal}{{/literal}
		$pid = 0;
	{literal}}{/literal}
	Menu::createMenu('Quản trị {$mod_name}', 'admin/{$mod_url}.html', 4, $pid, 'admin {$mod_name}', md5($module_name));

{if $mod_file}
	//tao thu muc anh neu co
	ImageUrl::createFolderImg('{$mod_key}', '{$mod_name_class}_FOLDER');
	ImageUrl::addSizeImg('{$mod_key}', 150, 0);
	ConfigSite::writeConfigImage();
{/if}
{literal}}{/literal}

function {$mod_name}_uninstall($module_name = ''){literal}{{/literal}
	//go bo bang
	DB::query("DROP TABLE IF EXISTS `".T_{$mod_name_class}."`");
	
	//go menu
	Menu::removeMenu(md5($module_name));
	
{if $mod_file}
	//go thu muc anh
	ImageUrl::removeFolderImg('{$mod_key}');
	ConfigSite::writeConfigImage();
{/if}	
{literal}}{/literal}