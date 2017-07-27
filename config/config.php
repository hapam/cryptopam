<?php

//set time zone
date_default_timezone_set('Asia/Bangkok');
define('TIME_NOW',time());

define('REQUEST_SCHEME', "http");

//path & doamin config
define('ROOT_PATH', str_replace(array('config/'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));
$webroot=str_replace('\\','/',REQUEST_SCHEME.'://'.$_SERVER['HTTP_HOST'].(dirname($_SERVER['SCRIPT_NAME'])?dirname($_SERVER['SCRIPT_NAME']):''));
$webroot.=$webroot[strlen($webroot)-1]!='/'?'/':'';
define('WEB_ROOT',$webroot);
unset($webroot);

//Website config
define('WEB_AUTHOR', 'SupermanDotPam');
define('OFF_SITE', 0); //config manual offline site

//main directory
define('DIR_MODULE', ROOT_PATH."modules/");
define('DIR_THEMES', ROOT_PATH.'themes/');
define('WEB_THEMES', WEB_ROOT.'themes/');

//token
define('TOKEN_KEY_NAME', '__myToken');
define('TOKEN_KEY_SECRET', '(|)..<O~~~newStarT');

//user & customer
define('USER_ACTIVE_ON', 1); // Bat active user & customer
define('USER_PASWORD_KEY', '-ShopTeam2010'); //encode pasword

//Session config
define( 'SESSION_TYPE' , 'file'); //memcache - session memcache | db - session database | file - session file
define('MEMCACHE_SESSION_HOST', "127.0.0.1");
define('MEMCACHE_SESSION_PORT', "11211");
define('_SESS_TIME_EXPIRE', "10800");

//rewrite URL
define('REWRITE_ON', 1);    //0: deactive | 1: active

//include other config
require_once('config.db.php');
require_once('config.image.php');
require_once('config.smtp.php');
require_once('config.cookie.php');
require_once('config.cache.php');
