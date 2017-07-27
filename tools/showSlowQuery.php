<?php
require_once '../config/config.php';//System Config...
require_once '../core/Debug.php';//System Config...
require_once '../core/Init.php';  //System Init..

$valCache = CacheLib::get('slowQuery');

require_once '../core/PageBegin.php';

echo '<div style="padding:20px">';
if(!empty($valCache)){
	foreach ($valCache as $v) {
		echo '<div style="margin:5px 0;font-size:12px">'.$v.'</div>';
	}
	echo '<div><a onclick="shop.deleteCache(\'slowQuery\')" href="javascript:void(0);">Delete</a></div>';
}
else{
	echo '<div> Không có query nào slow</div>';
}
echo '</div>';

require_once '../core/PageEnd.php';
