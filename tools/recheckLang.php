<?php
require_once '../core/Debug.php'; //System Debug...
require_once '../config/config.php';//System Config...
require_once '../core/Init.php';  //System Init...

$debug = 1;
if($debug){
	echo '<h1 align="center">Kiem tra loi trung tu khoa ngon ngu</h1>';
}
$existed = array();
$data = array();
$res = DB::query("SELECT * FROM ".T_LANG." WHERE is_main = 1");
while($r = @mysql_fetch_assoc($res)){
	$key = strtolower(StringLib::stripUnicode($r['title']));
	if(isset($data[$key])){
		if($debug){
			echo '<span style="color:red">--------------- Tu trung lap: <b>'.$key.'</b> ---------------</span><br />';
		}
		$existed[] = $r['id'];
	}else{
		if($debug){
			echo 'Luu tu khoa de kiem tra: <b>'.$key.'</b><br />';
		}
		$data[$key] = $r;
	}
}
if($debug){
	echo '<hr />';
}

if(!empty($existed)){
	$ids = implode(',', $existed);
	DB::delete(T_LANG, "id IN ($ids) OR pid IN ($ids)");
	if($debug){
		echo 'Tong cong co: <b style="color:blue">'.count($data).'</b> tu khoa';
		echo 'Da xoa: <b style="color:red">'.count($existed).'</b> tu trung lap';
	}
}else{
	echo '<p style="color:blue"><b>Khong tim thay tu bi trung lap</b></p>';
}