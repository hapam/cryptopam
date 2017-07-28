<?php
//config cache
define('CACHE_ON', 1);
define('PAGE_CACHE',1);
define('CACHE_DB', 1);

//cached directory
define('DIR_CACHE', ROOT_PATH."_cache/");
define('PAGE_CACHE_DIR',DIR_CACHE.'pages/');

//redis
define('REDIS_ON', 0);
$redis_server =  array(
    array("host"=>"127.0.0.1","port"=>"6379")
);

//memcached
define('MEMCACHE_ON', 0);
$memcache_server =  array(
  array("host"=>"127.0.0.1","port"=>"11211")
);
define('MEMCACHE_ID','ezTool'); //Dung lam ID phan biet session giua cac site mini shop

//cached file
$server_list=array('cryptopam.herokuapp.com');//server list to run multi cache file server
