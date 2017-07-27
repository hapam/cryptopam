<?php
class ImageUrl{
	//site bg
	static function getSiteBG ($path = ''){
        if($path != '' && !self::isCreated($path)){
			$path = self::getImageServerUrl().SITEINFO_FOLDER.$path;
		}
		return $path;
	}
	
	//logo
	static function getSiteLogo ($path = ''){
		if($path == ''){
			return DEFAULT_SITE_LOGO;
		}
		return self::getImageServerUrl().SITEINFO_FOLDER.$path.'?v='.CGlobal::$css_ver;
	}
	
	//water mask
	static function getWaterMask($path = ''){
		if($path == ''){
			return  self::getImageServerUrl() . 'code/default_img/water_mask.gif';
		}
		return self::getImageServerUrl().SITEINFO_FOLDER.$path.'?v='.CGlobal::$css_ver;
	}

	//favicon
	static function getSiteFavicon ($path = ''){
		if($path == ''){
			return DEFAULT_SITE_FAVICON;
		}
		return self::getImageServerUrl().SITEINFO_FOLDER.$path;
	}
	
	//get link image by time
	static function getImageURL($fname = '', $time = 0, $size = 0, $keyName = '', $folder = ''){
		if(!isset(CGlobal::$imageSize[$keyName]) || !isset(CGlobal::$imageSizeKeys[$keyName])){
			return $fname;
		}
		if($size == 0){
			$size = CGlobal::$imageSizeKeys[$keyName][0];
		}
		if($fname == ''){
			if(isset(CGlobal::$imageSize[$keyName][$size])){
				$fname = CGlobal::$imageSize[$keyName][$size]['width'];
			}else{
				$fname = CGlobal::$imageSizeKeys[$keyName][0];
			}
			return self::getImageServerUrl().NO_PHOTO.$keyName."_no_photo_".$size.".png";
		}
        if(self::isCreated($fname)){
            return $fname;
        }
		$dir = $folder . FileHandler::createdDirByTime($time) . FOLDER_PREFIX . $size;
		return self::getImageServerUrl() . $dir . '/' . $fname;
	}
	
	//get all image size from link
	static function getImageUrlFromLink($link = '', $key = ''){
		$images = CGlobal::$imageSizeKeys[$key];
		if($link != ''){
			$link = explode('/',$link);
			//get file name
			$file_name = array_pop($link);
			//remove size drirectory
			array_pop($link);
			//glue all after remove size
			$prefix = implode('/', $link);
			foreach($images as $v){
				$images[$v] = $prefix . '/' . "size$v" . '/' . $file_name;
			}
		}else{
			foreach($images as $v){
				$images[$v] = '';
			}
		}
		return $images;
	}

	//server image url
	static function getImageServerUrl(){
		return (IS_UPLOAD_IMAGE_SERVER ? REQUEST_SCHEME.'://' : WEB_ROOT) . IMAGE_PATH . IMAGE_SERVER_TEMP_PATH;
	}
    //check created
    static function isCreated($url = ''){
        return filter_var($url, FILTER_VALIDATE_URL);
    }
	//tu dong tao thu muc
	static function createFolderImg($folderName = '', $defined = ''){
        if ($folderName != '' && $defined != '') {
			$key = 'imageSize';
			$imgSize = ConfigSite::getConfigFromDB($key, array(), true);
			if(!isset($imgSize[$folderName])){
				$imgSize[$folderName] = array(
					'name' => $folderName,
					'defined' => $defined,
					'wm' => 0,
					'sizes' => array()
				);
				ConfigSite::setConfigToDB($key, serialize($imgSize));
				return true;
			}
		}
		return false;
    }
	static function removeFolderImg($name = ''){
		$key = 'imageSize';
		if ($name != '') {
			$imgSize = ConfigSite::getConfigFromDB($key, array(), true);
			if(isset($imgSize[$name])){
				unset($imgSize[$name]);
			}
			ConfigSite::setConfigToDB($key, serialize($imgSize));
			return true;
		}
		return false;
	}
	static function addSizeImg($name = '', $w = 0, $h = 0){
		$key = 'imageSize';
		if ($w > 0 || $h > 0) {
			$imgSize = ConfigSite::getConfigFromDB($key, array(), true);
			if(isset($imgSize[$name])){
				$k = $w > 0 ? $w : $h;
				$imgSize[$name]['sizes'][$k] = array(
					'w' => $w,
					'h' => $h
				);
				ConfigSite::setConfigToDB($key, serialize($imgSize));
				return true;
			}
		}
		return false;
	}
	
	//min - max
    static function getSize($key = '', $type = 'min'){
        $size = 0;
        if(isset(CGlobal::$imageSize[$key])){
            $sizes = CGlobal::$imageSize[$key];
            foreach($sizes as $k => $v){
                if($size == 0){
                    $size = $k;
                }else{
                    if($type == 'min'){
                        if($size > $k){
                            $size = $k;
                        }
                    }else{
                        if($size < $k){
                            $size = $k;
                        }
                    }
                }
            }
        }
        return $size;
    }
}
