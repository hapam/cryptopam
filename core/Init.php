<?php

require_once ROOT_PATH.'core/Table.php';
require_once ROOT_PATH.'core/SmartyFunction.php';
require_once ROOT_PATH.'core/AutoLoader.php';
require_once ROOT_PATH.'core/detectMobile.php';

//kiem tra phien ban mobile
$mode = CookieLib::get_cookie('websiteMode','');
if($mode == 'pc'){
    CGlobal::$mobile = false;
}else{
    CGlobal::$mobile = mobile_device_detect();
}
//load cached
CGlobal::$my_server	= $server_list;
if(MEMCACHE_ON){
	CGlobal::$memcache_server = $memcache_server;
	require_once ROOT_PATH.'includes/memcache.class.php';
}

//trigger exit
if(isset($_REQUEST['trigger']) && (int)$_REQUEST['trigger']==1) exit();

//get list page
CGlobal::$arrPage = CacheLib::get('arr_page',3600);
if(!CGlobal::$arrPage){
	$result = DB::select(T_PAGE);
	if(!empty($result)){
		foreach($result as $row){
			CGlobal::$arrPage[$row['name']] = $row;
		}
	}
	CacheLib::set('arr_page',CGlobal::$arrPage,3600);
}

//current page
CGlobal::$current_page = Url::fetchUrlArg();

//SEO engine
$is_search_engine_array = array("Google", "Fast", "Slurp", "Ink", "Atomz", "Scooter", "Crawler", "MSNbot", "Poodle", "Genius");
$is_search_engine = 0;
foreach($is_search_engine_array as $key => $val)  {
	if(strstr($_SERVER['HTTP_USER_AGENT'], $val)){
		$is_search_engine++;
	}
}

//session
if($is_search_engine == 0 && !defined('NO_SESSION')){
	if(SESSION_TYPE == 'memcache'){
		require_once(ROOT_PATH."includes/memcache.session.php"); //Session memcache store
	}
	else{
		if (session_id() === "") {session_start();}
	}
}

//get token data
FunctionLib::tokenData();

//load all configs
if(empty(CGlobal::$configs)){
	ConfigSite::get();
}

//kiem tra neu page sai thi redirect 301
$gohome = false;
if(isset(CGlobal::$arrPage[CGlobal::$current_page])){
	$cur_page = CGlobal::$arrPage[CGlobal::$current_page];
	if(CGlobal::$mobile[0] && CGlobal::$configs['themes_mobile'] != 'no_mobile'){
		if($cur_page['themes_mobile'] != '' && $cur_page['themes_mobile'] != CGlobal::$configs['themes_mobile']){
			$gohome = true;
		}
		//ko co layout thi thi redirect 301
		if(!$gohome && $cur_page['layout_mobile'] == ''){
			$gohome = true;
		}
	}else{
		if($cur_page['themes'] != '' && $cur_page['themes'] != CGlobal::$configs['themes']){
			$gohome = true;
		}
		//ko co layout thi thi redirect 301
		if(!$gohome && $cur_page['layout'] == ''){
			$gohome = true;
		}
	}
}else{
	$gohome = true;
}
if($gohome){
	Url::redirect_url(Url::buildURL(CGlobal::$defaultHomePage), 301);
}

//load language
Language::initLang(isset(CGlobal::$configs['lang']) ? CGlobal::$configs['lang'] : false);

//load site configs
$site_configs = isset(CGlobal::$configs['site_configs']) ? @unserialize(CGlobal::$configs['site_configs']) : array();

//trang website
CGlobal::$web_status = isset($site_configs['website_status']) ? $site_configs['website_status'] : 'online';
CGlobal::$web_status_txt = (isset($site_configs['alert_txt']) && !empty($site_configs['alert_txt'])) ? $site_configs['alert_txt'] : 'Thông báo nghỉ';
CGlobal::$web_status_img = DEFAULT_SITE_STOP;
if(isset($site_configs['alert']) && !empty($site_configs['alert'])){
	CGlobal::$web_status_img = ImageUrl::getSiteBG($site_configs['alert']);
}

//don vi tien te
CGlobal::$currency = isset($site_configs['currency']) ? $site_configs['currency'] : 'VNĐ';

//max upload size
$max_upload = isset($site_configs['upload_size']) ? intval($site_configs['upload_size']) : 1;
CGlobal::$max_upload_size = $max_upload*1024*1024;

//ten website 
CGlobal::$website_title = isset($site_configs['site_name']) ? $site_configs['site_name'] : 'superman';
CGlobal::$site_name = isset($site_configs['domain_name']) ? $site_configs['domain_name'] : DOMAIN;
CGlobal::$site_name = ucfirst (CGlobal::$site_name);
//logo & favicon, background
CGlobal::$logo_title = isset($site_configs['logo_title']) ? $site_configs['logo_title'] : '';
CGlobal::$logo_size = array(
    'width' => isset($site_configs['logo_size_width']) ? $site_configs['logo_size_width'] : 0,
    'height' => isset($site_configs['logo_size_height']) ? $site_configs['logo_size_height'] : 0
);
CGlobal::$logo = ImageUrl::getSiteLogo(isset($site_configs['logo']) ? $site_configs['logo'] : '');
CGlobal::$favicon = ImageUrl::getSiteFavicon(isset($site_configs['favicon']) ? $site_configs['favicon'] : '');
CGlobal::$background = ImageUrl::getSiteBG(isset($site_configs['background']) ? $site_configs['background'] : '');

//phien ban css va js
CGlobal::$version = (isset($site_configs['siteVer'])&&$site_configs['siteVer']!='')?$site_configs['siteVer']:CGlobal::$version;
CGlobal::$js_ver = (isset($site_configs['siteVer'])&&$site_configs['siteVer']!='')?$site_configs['siteVer']:CGlobal::$version;
CGlobal::$css_ver = (isset($site_configs['siteVer'])&&$site_configs['siteVer']!='')?$site_configs['siteVer']:CGlobal::$version;

//thoi gian thay doi mat khau admin
CGlobal::$changePassTime = isset($site_configs['change_pass']) ? $site_configs['change_pass'] : 0;

//thoi gian bat buoc dang nhap lai
CGlobal::$reLoginTime = isset($site_configs['relogin']) ? $site_configs['relogin'] : 0;

//dang nhap co su dung captcha
CGlobal::$adminCaptcha = isset($site_configs['captcha']) ? $site_configs['captcha'] : 0;
CGlobal::$captchaPublic = isset($site_configs['captcha_public']) ? $site_configs['captcha_public'] : '';
CGlobal::$captchaPrivate = isset($site_configs['captcha_private']) ? $site_configs['captcha_private'] : '';

//load image size
CGlobal::$imageSize = $image_sizes;

//get image size keys
foreach(CGlobal::$imageSize as $k => $v){
	CGlobal::$imageSizeKeys[$k] = array_keys($v);
}
//site keywords
if(isset(CGlobal::$configs['site_keywords'])){
	CGlobal::$keywords  =  CGlobal::$configs['site_keywords'];
	unset(CGlobal::$configs['site_keywords']);
}
//site description
if(isset(CGlobal::$configs['site_description'])){
	CGlobal::$meta_desc = CGlobal::$configs['site_description'];
	unset(CGlobal::$configs['site_description']);
}
//Set SEO for each page
if(isset(CGlobal::$arrPage[CGlobal::$current_page]) && !Url::isAdminUrl()){
	$curP = CGlobal::$arrPage[CGlobal::$current_page];
	if(!empty($curP['keyword'])){
		CGlobal::$keywords = $curP['keyword'];
	}
	if(!empty($curP['description'])){
		CGlobal::$meta_desc = $curP['description'];
	}
	if(!empty($curP['title'])){
		CGlobal::$website_title = $curP['title'] . ' | ' . CGlobal::$site_name;
	}
}
//black IP for ban
if(isset(CGlobal::$configs['black_ips'])){
    CGlobal::$black_ips = CGlobal::$configs['black_ips'];
	unset(CGlobal::$configs['black_ips']);
}
//load permission
if(isset(CGlobal::$configs['site_permission'])){
	CGlobal::$permission = @unserialize(CGlobal::$configs['site_permission']);
	unset(CGlobal::$configs['site_permission']);
}
if(empty(CGlobal::$permission_group)){
	CGlobal::$permission_group = CacheLib::get('user-roles', 86400*7, 'roles/');
	if(empty(CGlobal::$permission_group)){
		CGlobal::$permission_group = DB::fetch_all("SELECT * FROM ".T_ROLES." ORDER BY rank ASC");
		if(!empty(CGlobal::$permission_group)){
			CacheLib::set('user-roles', CGlobal::$permission_group, 86400*7, 'roles/');
		}
	}
}

//khoi tao toan bo init
if(isset(CGlobal::$configs['site_module_init']) && CGlobal::$configs['site_module_init'] == 1){
	ModuleInit::run();
}

//tu dong dang nhap trong quan tri
//if(Url::isAdminUrl()){
	//check admin login
	$password = md5('password');
	$user_id  = md5('uid');
	if(!User::is_login()){
		$password = CookieLib::get_cookie($password);
		$user_id = CookieLib::get_cookie($user_id);
		if($password && $user_id){
			User::check_auto_login(FunctionLib::hiddenID($user_id, true),$password);
		}
	}
	else if(!CookieLib::isCookieExisted($user_id)){
		User::LogOut();
	}else{
		$user_data = User::$current->data;
		if(CGlobal::$changePassTime > 0 && (($user_data['last_changepass']+CGlobal::$changePassTime*24*3600) < TIME_NOW)){
			//ko cho dang nhap tu dong ma bat nhap mat khau moi
			User::LogOut();
		}else if(CGlobal::$reLoginTime > 0 && (($user_data['last_login']+CGlobal::$reLoginTime*24*3600) < TIME_NOW)){
			//ko cho dang nhap tu dong ma bat dang nhap lai
			User::LogOut();
		}
	}
//}

//load display class
require_once ROOT_PATH.'includes/display.class.php';

global $display;
$display = new TplLoad();
if (get_magic_quotes_gpc()){
	function stripslashes_deep($value){
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	$_COOKIE  = array_map('stripslashes_deep', $_COOKIE);
}

//unset set not use
unset($site_configs, $memcache_server, $server_list, $image_sizes);

register_shutdown_function(array("DB","close"));
