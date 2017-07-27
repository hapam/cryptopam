<?php
	require_once('config.inc.php');
	
	$s = new SuperImageServer();
	
	$file_src  = ROOT.$_POST['file_path'];
	$imageInfo = getimagesize($file_src);
	$file_name = remove_sign_file($_POST['file_name']);
	$old_file  = $_POST['old_file'];
	$id = isset($_POST['id']) ? $_POST['id'] : 0;

	$all_img_name = getAllImages(AVATAR_FOLDER, $file_name, $avatar_image_sizes, $id);
	$old_img_name = getAllImages(AVATAR_FOLDER, $old_file, $avatar_image_sizes, $id);

	$aspect_ratio = ($imageInfo[0] > 0) ? ($imageInfo[1] / $imageInfo[0]) : 1;
	
	$error = '';
	if(is_array($all_img_name) && !empty($all_img_name)){
		foreach ($avatar_image_sizes as $k => $val){
			$oke = genImageFromSource($file_src,$all_img_name[$k],$imageInfo[0],$imageInfo[1],$val['width'],0,$aspect_ratio,false);
			if(!$oke){
				$error = 'AVATAR_'.$k.'_ERROR';
				break;
			}
		}
	}
	
	if($error == ''){
		$error = $s->setSuccess($file_name);
		//xoa file cu~
		if($old_file != '' && is_array($old_img_name) && !empty($old_img_name)){
			foreach ($avatar_image_sizes as $k => $val){
				if(is_file($old_img_name[$k])){
					@unlink($old_img_name[$k]);
				}
			}
		}
	}else{
		$error = $s->setError($error);
		//xoa cac file vua tao
		foreach ($avatar_image_sizes as $k => $val){
			if(is_file($all_img_name[$k])){
				@unlink($all_img_name[$k]);
			}
		}
	}
	//xoa file goc di
	if(is_file($file_src)){
		@unlink($file_src);
	}
	echo $error; 
	exit(); 
