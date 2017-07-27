<?php

class ConfigSite{
	static $key  = 'all-site-configs';
	static $time = 31536000;
	static $dir  = 'configs/';
	static $configs = array();
	
	static function get(){
		$key_need = array(
			'site_module_config',
			'site_module_init',
			'site_configs',
			'module_configs',
			'black_ips',
			'site_permission',
			'site_menu',
			'site_keywords',
			'site_description',
			'static_page_key',
			'themes',
			'themes_mobile',
			'admin_config',
			'other_config',
			'lang'
		);
		$key_need = implode("','", $key_need);
		
		$cached = CacheLib::get(self::$key, self::$time, self::$dir);
		if(empty($cached)){
			$cached = DB::fetch_all("SELECT * FROM ".T_CONFIGS." WHERE conf_key IN ('$key_need')");
			if(!empty($cached)){
				CacheLib::set(self::$key, $cached, self::$time, self::$dir);
			}
		}
		if(!empty($cached)){
			foreach($cached as $v){
				CGlobal::$configs[$v['conf_key']] = $v['conf_val'];
			}
		}
		return $cached;
	}
	
	static function setConfigToDB($key = '', $value = ''){
		$ok = DB::insert(T_CONFIGS, array(
            'conf_key' => $key,
            'conf_val' => $value
        ), true);

		//clear cached
		if($ok){
			CacheLib::delete($key, self::$dir);
		}
		return $ok;
    }
    static function getConfigFromDB($key, $default = '', $unserialize = false, $parent_key = ''){
        $getDB = false;
        if($parent_key != ''){
            if(isset(CGlobal::$configs[$parent_key])){
                if(is_string(CGlobal::$configs[$parent_key])){
                    $parent = unserialize(CGlobal::$configs[$parent_key]);
                    return isset($parent[$key]) ? ($unserialize ? unserialize($parent[$key]) : $parent[$key]) : $default;
                }
            }
            $getDB = true;
        }else{
            if(isset(CGlobal::$configs[$key])){
                return $unserialize ? unserialize(CGlobal::$configs[$key]) : CGlobal::$configs[$key];
            }
            $getDB = true;
        }
        if($getDB){
			//from cached
			$result = CacheLib::get($key,self::$time, self::$dir);
			if(empty($result)){
				$result = DB::fetch("SELECT * FROM ".T_CONFIGS." WHERE conf_key = '$key'");
			}
			if(!empty($result)){
				CacheLib::set($key, $result, self::$time, self::$dir);
			}
            $result = (!empty($result) && isset($result['conf_val'])) ? $result['conf_val'] : $default;
			return $unserialize ? @unserialize($result) : $result;
        }
        return $default;
    }
	
	//xoa cache config
	static function clearCacheConfig(){
		CacheLib::delete(self::$key, self::$dir);
	}

	static function addModuleConfig($key = '', $def = '', $config = array()){
		/*
			$config = array(
				'per' => '',
				'title' =>	'test',
				'type'	=>	'text' //type of html input: text, textare, checkbox, radio, select
				'options'   =>   array(k=>v, k1=> v1) //option for select, radio,
				'value' => default value
			);
		*/
		$config['value'] = self::getConfigFromDB($key,$def,false,'module_configs');
		$config['def'] = $def;
		self::$configs[$key] = $config;
	}
	
	static function saveModuleConfig(){
		if(!empty(self::$configs)){
			$save = array();
			$data = array();
			foreach(self::$configs as $k => $c){
				$data[$k] = Url::getParam($k, $c['def']);
			}
			if(!empty($data)){
				return self::setConfigToDB('module_configs', serialize($data));
			}
		}
		return false;
	}
	
	static function writeConfigImage(){
		$quality = ConfigSite::getConfigFromDB('img_fix',100,false,'site_configs');
		$synsAuto = ConfigSite::getConfigFromDB('img_genauto',1,false,'site_configs');

		$mask_min = ConfigSite::getConfigFromDB('water_mark_min',200,false,'site_configs');
		$mask_active = ConfigSite::getConfigFromDB('water_mark_active',0,false,'site_configs');
		$mask_position = ConfigSite::getConfigFromDB('water_mark','bottomright',false,'site_configs');
		$mask_image = ConfigSite::getConfigFromDB('water_mark_img','',false,'site_configs');
		$mask_margin = ConfigSite::getConfigFromDB('water_mark_margin',5,false,'site_configs');
		$mask_trans = ConfigSite::getConfigFromDB('water_mark_trans',30,false,'site_configs');

		$configSite = CGlobal::$configs;
		$key = 'imageSize';
		$imgSize = ConfigSite::getConfigFromDB($key, array(), true);
		$imgServer = ConfigSite::getConfigFromDB('imageServer', array(), true);
		
		//cau hinh server anh
		$imgServer['IMAGE_PATH'] = (($imgServer['img_server'] == 1) ? $imgServer['img_domain'] : '_img_server').'/';
		$imgServer['IMAGE_SERVER_TEMP_PATH'] = ($imgServer['img_server'] == 1) ? $imgServer['img_tmp_dir'] : '';
		if($imgServer['IMAGE_SERVER_TEMP_PATH'] != ''){
			$imgServer['IMAGE_SERVER_TEMP_PATH'] .= '/';
		}
		
		//build file client
		$client = '<?php
//server image
define("IS_UPLOAD_IMAGE_SERVER", '.$imgServer['img_server'].');
define("IMAGE_PATH", "'.$imgServer['IMAGE_PATH'].'");
define("IMAGE_SERVER_TEMP_PATH", "'.$imgServer['IMAGE_SERVER_TEMP_PATH'].'");
define("IMAGE_PATH_STATIC","_img_server/");
define("IMAGE_CODE_DIR","code/");'.($imgServer['img_server'] == 1 ? '
define("FTP_IMAGE_SERVER","'.$imgServer['ftp_host'].'");
define("FTP_IMAGE_USER","'.$imgServer['ftp_user'].'");
define("FTP_IMAGE_PASSWORD","'.$imgServer['ftp_pass'].'");':'').'

//default image
define("DEFAULT_SITE_LOGO", WEB_ROOT."css/images/logo.png");
define("DEFAULT_SITE_FAVICON", WEB_ROOT."css/images/favicon.gif");
define("DEFAULT_SITE_STOP", WEB_ROOT."css/images/stop.jpg");
define("SITEINFO_FOLDER", "siteInfo/");

//defined for image creator
define("NO_PHOTO", "no_photo/");
define("FOLDER_PREFIX", "size");

//water mask
if(!defined("IMG_QUALITY")) define("IMG_QUALITY", '.$quality.');
if(!defined("MASK_ACTIVE")) define("MASK_ACTIVE", '.$mask_active.');
if(!defined("MASK_POSITION")) define("MASK_POSITION", "'.$mask_position.'");
if(!defined("MASK_IMG")) define("MASK_IMG", "'.$mask_image.'");
if(!defined("MASK_TRANS")) define("MASK_TRANS", '.$mask_trans.');
if(!defined("MASK_MARGIN")) define("MASK_MARGIN", '.$mask_margin.');
if(!defined("MASK_MIN")) define("MASK_MIN", '.$mask_min.');

//Folder images';
		$clientArr = '';
		$server = '<?php
if(!defined("IMG_QUALITY")) define("IMG_QUALITY", '.$quality.');
if(!defined("IMG_GEN_AUTO")) define("IMG_GEN_AUTO", '.$synsAuto.');
if(!defined("MASK_ACTIVE")) define("MASK_ACTIVE", '.$mask_active.');
if(!defined("MASK_POSITION")) define("MASK_POSITION", "'.$mask_position.'");
if(!defined("MASK_IMG")) define("MASK_IMG", "'.$mask_image.'");
if(!defined("MASK_TRANS")) define("MASK_TRANS", '.$mask_trans.');
if(!defined("MASK_MARGIN")) define("MASK_MARGIN", '.$mask_margin.');
if(!defined("MASK_MIN")) define("MASK_MIN", '.$mask_min.');

global $imageConfigSize;
$imageConfigSize = array(';
		foreach($imgSize as $k => $v){
			$client .= '
define("'.$v['defined'].'", "'.$v['name'].'/");
';
			$server .= '
	"'.$v['name'].'" => array(
		"folder" => "'.$v['name'].'/",
		"mask" => '.(isset($v['wm']) ? $v['wm'] : 0).',
		"sizes"  => array(';
			if(isset($v['sizes']) && !empty($v['sizes'])){
				$clientArr .= '
	"'.$v['name'].'" => array(';
				foreach($v['sizes'] as $ksize => $size){
					$clientArr .= '
		'.$ksize.'	=> array("width" => '.$size['w'].', "height" => '.$size['h'].'),';
					$server .= '
			'.$ksize.'	=> array("width" => '.$size['w'].', "height" => '.$size['h'].'),';
				}
				$clientArr .='
	),';
			}
			$server .= '
		)
	),';
		}
		$server .= '
);';
		if($clientArr != ''){
			$clientArr = '
$image_sizes = array('.$clientArr.'
);';
		}
		$client .= $clientArr;
		
		//ghi file client
		$client_file = ROOT_PATH . 'config/config.image.php';
		$handle = @fopen($client_file, "w");
		if ($handle) {
			fwrite($handle, $client);
			fclose($handle);
		}

		//ghi file tmp
		$client_file = ROOT_PATH . IMAGE_PATH_STATIC . IMAGE_CODE_DIR . 'config.imageSize.php';
		$handle = @fopen($client_file, "w");
		if ($handle) {
			fwrite($handle, $server);
			fclose($handle);
		}
		//upload len FTP neu co
		if(IS_UPLOAD_IMAGE_SERVER){
			FileHandler::uploadFileFTP($client_file, IMAGE_CODE_DIR . 'config.imageSize.php');
		}
		return $client;
	}
}