<?php
require_once 'config/config.php';//System Config...
require_once 'core/CGlobal.php';
require_once 'core/CacheLib.php';
CGlobal::$my_server	=	$server_list;
$post = $_POST;
$cacheKey = isset($post['cacheKey']) ? $post['cacheKey'] : '';
$value = isset($post['value']) ? $post['value'] : '';
$expire = isset($post['expire']) ? $post['expire'] : '';
$subDir = isset($post['subDir']) ? base64_decode($post['subDir']) : '';
if($cacheKey){
	CacheLib::set($cacheKey,$value,$expire,$subDir,true);
}
