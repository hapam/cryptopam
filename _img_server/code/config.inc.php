<?php
if(!defined('TIME_NOW')){
	putenv("Asia/Saigon");
	date_default_timezone_set('Asia/Bangkok');

	define('TIME_NOW',time());
}

//image config && function
if(!defined('FOLDER_PREFIX'))	define('FOLDER_PREFIX', 'size');
if(!defined('ORIGIN_FOLDER'))	define('ORIGIN_FOLDER', 'origin');
if(!defined('TEMP_FOLDER'))		define('TEMP_FOLDER', 'tmp/');

define('ROOT', str_replace(array('code/'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));

require_once('image.inc.php');
require_once('config.imageSize.php');

/*
	class image server
*/
class SuperImageServer{
	//define data image sizes & folder
	public $dataImage = array();
	
	public $param = array();
	
	public $msg = '';
	
	public function __construct(){
		global $imageConfigSize;
		$this->dataImage = $imageConfigSize;
	}
	
	public function createImageFromSrc($src = '', $des = '', $sizes = array(), &$imageInfo = '', $water_mask = false){
		$imageInfo = getimagesize($src);
		$aspect_ratio = ($imageInfo[0] > 0) ? ($imageInfo[1] / $imageInfo[0]) : 1;
		
		//tu dong co anh theo khung
		if($sizes['width'] > 0 && $sizes['height'] > 0){
			//anh doc fix theo doc
			if($imageInfo[0] < $imageInfo[1]){
				$sizes['width'] = 0;
			}else{
				$sizes['height'] = 0; // fix theo anh ngang
			}
		}

		$desFolder = dirname($des);
		$this->make_dir($desFolder);
	
		return genImageFromSource($src, $des, $imageInfo[0], $imageInfo[1], $sizes['width'], $sizes['height'], $aspect_ratio, $water_mask);
	}
	
	public function createImageBySizes($src = '', $fname = '', $time = '', $desFolder = '', $sizes = array(), $old_file = '', $water_mask = false){
		$src  = ROOT . $src;
		$imageInfo = getimagesize($src);
		$aspect_ratio = ($imageInfo[0] > 0) ? ($imageInfo[1] / $imageInfo[0]) : 1;
		$error = '';
		
		$rootDir   = ROOT . $desFolder . $this->createdDirByTime($time);
		$originDir = $rootDir . ORIGIN_FOLDER;
		$desDir    = $rootDir . FOLDER_PREFIX;
		
		//copy file goc vao thu muc origin
		if(is_file($src)){
			$this->make_dir($originDir);
			$oke = genImageFromSource($src, $originDir.'/'.$fname, $imageInfo[0], $imageInfo[1], $imageInfo[0], $imageInfo[1], 1, false);
		}
		
		$del_old = false;
		if($oke){
			if(IMG_GEN_AUTO){
				$del_old = true;
				$error = $this->setSuccess($fname);
			}else{
				//create directory & image by size
				foreach($sizes as $k => $v){
					$new_dir  = $desDir.$k;
					$this->make_dir($new_dir);
					$w = $v['width'];
					$h = $v['height'];
					if($v['width'] > 0 && $v['height'] > 0){
						//anh doc fix theo doc
						if($imageInfo[0] < $imageInfo[1]){
							$w = 0;
						}else{
							$h = 0; // fix theo anh ngang
						}
					}

					$oke = genImageFromSource($src, $new_dir.'/'.$fname, $imageInfo[0], $imageInfo[1], $w, $h, $aspect_ratio, $water_mask);
					if(!$oke){
						$error = 'IMAGE_SIZE_ERROR_'.$k;
						break;
					}
				}
				
				//process result
				if($error == ''){
					$del_old = true;
					$error = $this->setSuccess($fname);
				}else{
					$error = $this->setError($error);
					$this->removeFileBySize($fname, $sizes, $desDir); //xoa cac file vua tao
				}
			}
		}else{
			$error = 'IMAGE_ERROR';
		}
		
		if($old_file != '' && $del_old){
			$this->removeFileBySize($old_file, $sizes, $desDir); //xoa file cu~
			//xoa file goc cu
			$old_file = $originDir . '/' . $old_file;
			if(is_file($old_file)){
				@unlink($old_file);
			}
		}
	
		//xoa file goc
		if(is_file($src)){
			@unlink($src);
		}
	
		return $error;
	}
	
	public function imageRotate($fname = '', $time = '', $key = '', $degrees = 0, $bg_color = 0, $water_mask = false){
		$desFolder = $this->dataImage[$key]['folder'];
		$sizes = $this->dataImage[$key]['sizes'];

		$rootDir   = ROOT . $desFolder . $this->createdDirByTime($time);
		$originDir = $rootDir . ORIGIN_FOLDER;
		$desDir    = $rootDir . FOLDER_PREFIX;

		//copy de file origin
		$err = image_rotate($originDir.'/'.$fname, $originDir.'/'.$fname, $degrees, $bg_color);
		if($err == "DONE"){
			//xoa cac file cu
			$this->removeFileBySize($fname, $sizes, $desDir);
		}
		return $err;
	}
	
	public function removeFileBySize($fname = '', $sizes = array(), $desDir = ''){
		foreach ($sizes as $k => $v){
			$del_file  = $desDir . $k . '/' . $fname;
			if(is_file($del_file)){
				@unlink($del_file);
			}
		}
	}
	
	public function createdDirByTime($time = 0){
		$time = $time > 0 ? $time : TIME_NOW;
		return date('Y/m/d/', $time);
	}
	
	public function make_dir($path){
		$pathArr = explode('/',$path);
		$dir = '';
		$start = 0;
		foreach ($pathArr as $val){
			if($start == 0){
				$dir .= $val;
				$start= 1;
			}else
				$dir .= '/'.$val;
			if(!is_dir($dir)){
				@mkdir($dir, 0755);
			}
		}
	}
	
	public function setSuccess($msg = '', $mix = array()){
		$arr = array('err' => 0, 'msg' => $msg);
		if(!empty($mix)){
			$arr = $arr + $mix;
		}
		return serialize($arr);
	}
	
	public function setError($msg = '', $mix = array()){
		$arr = array('err' => -1, 'msg' => $msg);
		if(!empty($mix)){
			$arr = $arr + $mix;
		}
		return serialize($arr);
	}
	
	public function getParam($name = '', $def = ''){
		$client = (!empty($this->param) && isset($this->param['from_client'])) ? $this->param['from_client'] : 0;
		if($client == 1){
			if(!empty($this->param) && isset($this->param[$name])){
				return $this->param[$name];
			}
		}elseif(isset($_POST[$name])){
			return $_POST[$name];
		}
		return $def;
	}
}
