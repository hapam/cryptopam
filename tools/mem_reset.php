<?php
	
	require_once '../core/Debug.php'; //System Debug...
	require_once '../config/config.php';//System Config...
	require_once '../core/Init.php';  //System Init...
	
	if(MEMCACHE_ON){
		if(memcacheLib::clear()){		
			echo "done";		
			exit();
		}
	}
