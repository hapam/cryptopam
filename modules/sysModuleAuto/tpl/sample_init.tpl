<?php
//dinh nghia key
if(!defined('{$mod_key}_KEY')){literal}{{/literal}
	define('{$mod_key}_KEY', '{$mod_key_name}');
{literal}}{/literal}

//dinh nghia bang
if(!defined('T_{$mod_key}')){literal}{{/literal}
	global $prefix;
	define('T_{$mod_key}', $prefix . {$mod_key}_KEY);
{literal}}{/literal}

{if $mod_check.file}
//dinh nghia duong dan thu muc anh
if(!defined('{$mod_key}_FOLDER')){literal}{{/literal}
	ImageUrl::createFolderImg({$mod_key}_KEY, '{$mod_key}_FOLDER');
{literal}}{/literal}{/if}