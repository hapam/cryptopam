<?php
require_once './config/config.php';//System Config...
require_once ROOT_PATH.'core/CGlobal.php';
require_once ROOT_PATH.'core/DB.php';

if(SESSION_TYPE == 'db'){
	//Session db store
	require_once ROOT_PATH.'includes/session.class.php';
}
elseif(SESSION_TYPE == 'memcache'){
	//Session memcache store
	require_once(ROOT_PATH."includes/memcache.session.php");	
}
else{		
	session_start(); 
}
$alphanum = "ABCDEFGHIKLMOPQRSTUVWXYZ123456789";
$width = isset($_REQUEST['w']) ? $_REQUEST['w'] : 50;	//chieu rong cua anh
$height= isset($_REQUEST['h']) ? $_REQUEST['h'] : 20;	//chieu cao cua anh
$length = isset($_REQUEST['l']) ? $_REQUEST['l'] : 3;	//do dai cua xau ki tu
$area = 10;	//kich thuoc canh cua o vuong nho lam nhieu
$loop1 = ceil($height/$area);	//so o vuong theo chieu doc
$loop2 = ceil($width/$area);	//so o vuong theo chieu ngang
$text = substr(str_shuffle($alphanum), 0, $length); //chu can in ra

//dung de valid
$_SESSION["captcha_validate"] = array(
	'time' => time(),
	'error'=> 0,
	'txt'  => $text
);

//khoi tao hinh anh
$im = ImageCreate($width, $height);

//tao mau
$white = imagecolorallocate($im, 255, 255, 255);
$red = imagecolorallocate($im, 255, 0, 0);
$orange = imagecolorallocate($im, 255, 192, 192);

//tao nen
imagefill($im, 0, 0, $white);
imagerectangle($im, 0, 0, $width-1, $height-1, $orange);

//tao cac o vuong duoi nen
for($i=0;$i<$loop1;$i++){
	$y = $i*$area;
	$x = 0;
	for($j=0;$j<$loop2;$j++){
		$x = $j*$area;
		imagerectangle($im, $x, $y, $x+$area, $y+$area, $orange);
	}
}

//in ra chuoi
imagestring($im, 5, round(($width-$length*9)/2), round(($height-14)/2), $text, $red);

//tra ve anh captcha
header("Content-type: image/png");
imagepng($im);
imagedestroy($im);