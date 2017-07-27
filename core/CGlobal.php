<?php
class CGlobal{
	static $version	= 5.773;
	static $js_ver	= 5.773;
	static $css_ver	= 5.773;

	//all configs value from DB
	static $configs	= array();
	
	//for debug
	static $cacheDebug = array(
		'redis' => array('get' => array(), 'set' => array()),
		'mem'  	=> array('get' => array(), 'set' => array()),
		'file' 	=> array('get' => array(), 'set' => array())
	);
	static $query_time;
	static $query_debug		= 	"";
    static $arrModuleDebug	= 	array();
	static $conn_debug 		= 	"";

	//website info
	static $web_status = 'online';
	static $web_status_img = '';
	static $web_status_txt = '';
	static $site_name = "";
	static $logo_title = '';
	static $logo = '';
    static $logo_size = '';
	static $favicon = '';
	static $background = '';
	static $currency = "đ";
	static $max_upload_size = 1;
	static $black_ips = '';
	static $breadcrumb = array();

	//SEO
	static $website_title = "";
	static $keywords = "";
	static $meta_desc = "";
	static $robotContent = 'INDEX, FOLLOW';
	static $gBContent ="index,follow,archive";
	static $pg_noIndex = array ('sign_out','error');

    //redis
    static $redis_server	=false;
    static $redis			=false;

	//memcache
	static $memcache_connect_id = false;
	static $memcache_server = false;
	
	//cache file
	static $my_server =	array ();

	//image defined
	static $ftp_image_connect_id = array ();
	static $imageSize = array();	
	static $imageSizeKeys = array();
	
	//province & support info
	static $messenger_support = array();
	static $province = array();
	static $province_active = array();

	//for core modules & pages
	static $coreModules = array(
		'sysProvince' => 'Tỉnh/Thành',
		'sysPanel' => 'Menu Admin',
		'sysPages' => 'Pages',
		'sysModule' => 'Modules',
		'sysModuleAuto' => 'Modules Writer',
		'sysLayout' => 'Layout',
		'sysUser' => 'Quản trị Admin',
		'sysUserRole' => 'Cấu hình Quyền',
		'sysLogin' => 'Login',
		'sysAdminPanel' => 'Admin Pannel',
		'sysConfigSite' => 'Cấu hình website',
		'sysStaticPage' => 'Trang tĩnh',
        'sysThemes' => 'Cấu hình themes',
        'sysLanguage' => 'Cấu hình ngôn ngữ',
        'EmailTemplateViewer' => 'Email mẫu',
		'amMenu' => 'Menu',
		'amGallery' => 'Thư viện ảnh'
	);
	static $arrPage = array(); //store all pages info
	static $corePages = array(
		'themes' => 21,
		'module' => 3,
		'page' => 1,
		'edit_page' => 2,
		'admin' => 4,
		'sign_out' => 5,
		'export' => 10,
		'admin_login' => 23
	);
	static $noDeletePages = array(
		'error' => 6,
		'access_denied' => 7,
		'home' => 8,
		'trang_tinh' => 9
	);
	
	//website URL
	static $current_page = "";
	static $defaultHomePage = 'home';
	static $urlArgs = array(); //router
	static $urlVars = array(); //variables
	
	//permissions
	static $permission = array();
	static $permission_group = array();
	
	//Can cu de xem user admin co online hay khong
	static $checkOnlineTime = 1800;
	
	//bat buoc phai dang nhap lai sau x ngay
	static $reLoginTime = 0;
	
	//thoi gian thay doi mat khau trong admin
	static $changePassTime  = 0;	
	
	//cau hinh on/off catcha khi dang nhap admin
	static $adminCaptcha  	= 0;	
	static $captchaPublic  	= '';
	static $captchaPrivate 	= '';
	
	//luu token de valid cac action tren website
	static $tokenData = '';
	
	//check mobile or web
	static $mobile = array(0);
	
	//luu cac bien static khac
	static $_V = array();	
	static function set($key = '', $val = '', $note = ''){
		if($key != ''){
			self::$_V[$key] = $val;
		}
	}	
	static function get($key = '', $def = ''){
		return isset(self::$_V[$key]) ? self::$_V[$key] : $def;
	}
}
