<?php
$rtime 		= microtime();
$rtime 		= explode(" ",$rtime);
$rtime 		= $rtime[1] + $rtime[0];
$start_rb 	= $rtime;
if( !defined('ROOT_PATH2') ){
	define('ROOT_PATH2', str_replace(array('/core'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));
}
$path = str_replace('/tools', '', ROOT_PATH2);
require_once $path.'core/Debug.php'; //System Debug...
require_once $path.'config/config.php';//System Config...
require_once $path.'core/Init.php';  //System Init...

$server_list = CGlobal::$my_server;
$del_c = Url::getParamInt('del_c', 0);
$msg = Url::getParamInt('msg', 0);

if((count($server_list) == 1) || $del_c == 2010){
	FunctionLib::empty_all_dir(DIR_CACHE,true,true);
	if($msg == 1){
		echo 'done';
	}
}
elseif(!empty($server_list)){
	foreach ($server_list as $server){
		$link = REQUEST_SCHEME."://{$server}tools/delcache.php?del_c=2010&msg=1";

		$curl = new CURL();
		$return = $curl->get($link);
		if($return == 'done'){
			echo "run service in $link<br>";
		}
		else{
			echo "error in $link<br>";
		}
	}
	//$mtime = microtime();
	//$mtime = explode(" ",$mtime);
	//$mtime = $mtime[1] + $mtime[0];
	//$end_rb = $mtime;			
	//$page_load_time = round(($end_rb - $start_rb),5)."s";
	//echo "Done in $page_load_time". "<br>";
}
