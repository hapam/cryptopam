<?php
//dinh nghia key
if(!defined('GALLERY_KEY')){
	define('GALLERY_KEY', 'gallery');
}

//dinh nghia bang
if(!defined('T_GALLERY')){
	global $prefix;
	define('T_GALLERY', $prefix.GALLERY_KEY);
	define('T_GALLERY_CATS', $prefix.GALLERY_KEY.'_cats');
}

//dinh nghia duong dan thu muc anh
if(!defined('GALLERY_FOLDER')){
	ImageUrl::createFolderImg(GALLERY_KEY,'GALLERY_FOLDER');
}