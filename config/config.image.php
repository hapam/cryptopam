<?php
//server image
define("IS_UPLOAD_IMAGE_SERVER", 0);
define("IMAGE_PATH", "_img_server/");
define("IMAGE_SERVER_TEMP_PATH", "");
define("IMAGE_PATH_STATIC","_img_server/");
define("IMAGE_CODE_DIR","code/");

//default image
define("DEFAULT_SITE_LOGO", WEB_ROOT."css/images/logo.png");
define("DEFAULT_SITE_FAVICON", WEB_ROOT."css/images/favicon.gif");
define("DEFAULT_SITE_STOP", WEB_ROOT."css/images/stop.jpg");
define("SITEINFO_FOLDER", "siteInfo/");

//defined for image creator
define("NO_PHOTO", "no_photo/");
define("FOLDER_PREFIX", "size");

//water mask
if(!defined("IMG_QUALITY")) define("IMG_QUALITY", 100);
if(!defined("MASK_ACTIVE")) define("MASK_ACTIVE", 0);
if(!defined("MASK_POSITION")) define("MASK_POSITION", "bottomright");
if(!defined("MASK_IMG")) define("MASK_IMG", "");
if(!defined("MASK_TRANS")) define("MASK_TRANS", 30);
if(!defined("MASK_MARGIN")) define("MASK_MARGIN", 5);
if(!defined("MASK_MIN")) define("MASK_MIN", 200);

//Folder images
define("GALLERY_FOLDER", "gallery/");

define("BACKUP_FOLDER", "backup/");

define("COIN_FOLDER", "crypto/");

$image_sizes = array(
	"gallery" => array(
		150	=> array("width" => 150, "height" => 0),
		350	=> array("width" => 350, "height" => 0),
		640	=> array("width" => 640, "height" => 0),
	),
	"crypto" => array(
		150	=> array("width" => 150, "height" => 0),
	),
);