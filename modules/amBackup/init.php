<?php
//dinh nghia key
if(!defined('BACKUP_KEY')){
	define('BACKUP_KEY', 'backup');
}

//dinh nghia bang
if(!defined('T_BACKUP')){
	global $prefix;
	define('T_BACKUP', $prefix.BACKUP_KEY);
}

//dinh nghia duong dan thu muc anh
if(!defined('BACKUP_FOLDER')){
	ImageUrl::createFolderImg(BACKUP_KEY,'BACKUP_FOLDER');
}