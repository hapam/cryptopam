<?php
require_once('config.inc.php');

$s = new SuperImageServer();

require_once('image.inc.php');

function notfound(){
	header("HTTP/1.0 404 Not Found"); 
	die("<h1>404 - Not Found!!!</h1>");
}

$dir	=	(dirname($_SERVER['SCRIPT_NAME'])?dirname($_SERVER['SCRIPT_NAME']):'');
$dir	=	str_replace('\\','/',$dir);
$uri	=	$_SERVER['REQUEST_URI'];

if($dir && $dir!='./'){
	if($dir[strlen($dir)-1] != '/'){
		$dir .= '/';
	}
	$dir = substr($dir, 0, strlen($dir) - 5); // code/ = 5 ki tu
	$url = substr($uri, strlen($dir), strlen($uri)-strlen($dir));
}

if(preg_match("/\.(jpg|png|gif|jpeg)$/is",$uri)){//View thumb image with width & heigh
	$urlArr = explode('/', $url);
	if(!isset($s->dataImage[$urlArr[0]]) && ($urlArr[0] != 'no_photo')){ //neu ko chua key folder thi bao loi
		notfound();
	}
	
	$nophoto = 0;
	$success = false;
	$key = '';
	if(isset($s->dataImage[$urlArr[0]])){ // neu key da duoc khai bao trong config
		if(count($urlArr) == 6){ // neu tham so truyen vao du
			$size = $urlArr[4];
			$size = substr($size, 4, strlen($size) - 4);
			$key = $urlArr[0];
			if(isset($s->dataImage[$urlArr[0]]['sizes'][$size])){ // neu kich thuoc truyen len la dung
				$urlArr[4] = 'origin';
				$src = ROOT . implode('/', $urlArr);
				$url = ROOT . $url;
				
				$path = pathinfo($url);
				if(!empty($path['extension'])){
					$file_ext = $path['extension'];
				}else{
					$file_ext = 'jpg';
				}
				//tao thu muc neu chua co
				global $imageConfigSize;
				$success = @$s->createImageFromSrc($src, $url, $s->dataImage[$urlArr[0]]['sizes'][$size], $info, $s->dataImage[$urlArr[0]]['mask'] == 1);
				if(!$success){
					$success = true;
					$nophoto = 1;
				}
			}else{
				notfound();
			}
		}else{
			notfound();
		}
	}elseif(stripos($urlArr[1], '_no_photo_') !== false){ // TH sinh anh bao loi thieu anh, do client truyen len
		$sArr = explode('_no_photo_', $urlArr[1]);
		if(count($sArr) == 2 && isset($s->dataImage[$sArr[0]])){ //key nhan dc phai dc khai bao truoc trong config
			$sArr[1] = substr($sArr[1], 0, -4);
			$nophoto = intval($sArr[1]);
			$key = $sArr[0];
			//kiem tra xem kich thuoc da dc khai bao chua
			$success = ($nophoto > 0 && isset($s->dataImage[$sArr[0]]['sizes'][$nophoto]));
		}else{
			notfound();
		}
	}
	if($success){
		//sinh anh bao loi
		if($nophoto > 0){
			$src = ROOT . 'code/default_img/'.($key=='avatar'?'no_avatar.jpg':'no_photo.png');
			$url = ROOT . implode('/', $urlArr);
			$success = $s->createImageFromSrc($src, $url, array('width' => $nophoto, 'height' => $nophoto), $info);
		}
		if($success){
			//show img
			header('HTTP/1.0 200 OK');//HTTP/1.1 200 OK
			header('Status: 200 OK'); //CGI method # 
			
			header("Expires: ");
			header("Last-Modified: ");
			header('Cache-Control: public,max-age=2592000'); //Adjust maxage appropriately
			header('Pragma: public');//header("Cache-Control: public, must-revalidate");header("Pragma: hack");
			
			if(stripos($_SERVER['HTTP_USER_AGENT'],"msie")===false){
				header('Content-type: '.$info['mime']);
			}
			
			echo file_get_contents($url);
		}
	}
}
notfound();

