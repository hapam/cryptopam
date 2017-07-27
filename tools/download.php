<?php
ignore_user_abort(true);
set_time_limit(0); // disable the time limit for this script

if( !defined('ROOT_PATH2') ){
	define('ROOT_PATH2', str_replace(array('/core'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));
}
$path = str_replace('/tools', '', ROOT_PATH2);
require_once $path.'config/config.php';//System Config...

$name = isset($_GET['f']) ? $_GET['f'] : '';
$time = isset($_GET['t']) ? $_GET['t'] : 0;
$dir = isset($_GET['d']) ? ($_GET['d'].'/') : '';

if($name != ''){
	if($time > 0){
		$dir .= createdDirByTime($time);
	}
	$fullPath = ROOT_PATH . IMAGE_PATH . IMAGE_SERVER_TEMP_PATH . $dir . $name;
	
	if ($fd = fopen ($fullPath, "r")) {
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);

		header("Content-type: application/octet-stream");
		header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
		header("Content-length: $fsize");
		header("Cache-control: private"); //use this to open files directly
		while(!feof($fd)) {
			$buffer = fread($fd, 2048);
			echo $buffer;
		}
	}
	fclose ($fd);
	exit;
}

function createdDirByTime($t = 0){
	$t = $t > 0 ? $t : time();
	return date('Y/m/d/',$t);
}