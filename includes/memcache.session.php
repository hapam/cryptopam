<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}
//TuanNK 20090113


//maybe we create new function for handling this
function init_session_cookies($path="/", $domain="") {
  if ($domain=='localhost') $domain='';
  if (function_exists('session_set_cookie_params')) {
    session_set_cookie_params(0, $path, $domain);
  } else {
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.cookie_path', $path);
    ini_set('session.cookie_domain', $domain);
  }
}

$cookie_path = '/';
if(preg_match(DOMAIN_COOKIE_REG_STRING, $_SERVER['HTTP_HOST'])){
	$cookie_domain = DOMAIN_COOKIE_STRING; //or any valid domain
}
else{
	$cookie_domain = "localhost";
}


//$session_save_path = "tcp://$host:$port?persistent=1&weight=2&timeout=2&retry_interval=10,  ,tcp://$host:$port  ";
$session_save_path = "tcp://".MEMCACHE_SESSION_HOST.":".MEMCACHE_SESSION_PORT."?persistent=1&weight=2&timeout=2&retry_interval=10,  ,tcp://".MEMCACHE_SESSION_HOST.":".MEMCACHE_SESSION_PORT."  ";

if (defined('DEBUG') && DEBUG && class_exists('CGlobal') && isset($start_rb)){
	global $start_rb;
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$end_rb = $mtime;			
	$load_time = round(($end_rb - $start_rb),5)."s";					 		
	CGlobal::$conn_debug.= " <b>Connect to Memcache session server : ".MEMCACHE_SESSION_HOST." : ".MEMCACHE_SESSION_PORT." </b> [in $load_time]<br>\n";
}			
		
init_session_cookies($cookie_path, $cookie_domain);
//ini_set('session.gc_divisor',100);
//ini_set('session.gc_probability',    1);	
ini_set('session.gc_maxlifetime', _SESS_TIME_EXPIRE);
ini_set('session.save_handler', 'memcache');
ini_set('session.save_path', $session_save_path);

// below sample main
session_start();
//session_regenerate_id(true);
