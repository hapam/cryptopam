<?php
ob_start();//start buffering

$rtime 		= microtime();
$rtime 		= explode(" ",$rtime);
$rtime 		= $rtime[1] + $rtime[0];
$start_rb 	= $rtime;

require_once 'core/Debug.php'; //System Debug...
require_once 'config/config.php';//System Config...

if(OFF_SITE){
  Header('Location: '.WEB_ROOT.'offline.htm');
}

require_once 'core/Init.php';  //System Init...


//load redis
if(REDIS_ON){
    CGlobal::$redis_server = $redis_server;
    CGlobal::$redis = new redisPhp();
    ReCache::set('test_key', 'ok! hello world !!!', 86400);
    echo ReCache::get('test_key');exit();
}

//chan black ip luon o day
if(FunctionLib::isBlackIP()){
  exit('<h1>Access is denied !!!</h1>');
}

if(CGlobal::$web_status == 'offline' && !User::user_access('offsite mode') && stripos($_SERVER['REQUEST_URI'],'admin/login.html') === false){
  Header('Location: '.WEB_ROOT.'offline.htm');
}
Layout::Run();//System process & output...