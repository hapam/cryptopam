<?php
    require_once('config.inc.php');
	
	$s = new SuperImageServer();
	
	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$img_src = isset($_POST['img_src']) ? $_POST['img_src'] : 'none';
	$err = false;
	$file_name= remove_sign_file('avatar.png');

	if($img_src != 'none' && $id != 0){
		$error = '';
		$imageInfo = array(120,120);
		$aspect_ratio = ($imageInfo[0] > 0) ? ($imageInfo[1] / $imageInfo[0]) : 1;
		$all_img_name = getAllImages(AVATAR_FOLDER, $file_name, $avatar_image_sizes, $id);
		$err = false;
		if(is_array($all_img_name) && !empty($all_img_name)){
			$img_type = 'png';
			if($im = @imagecreatefrompng($img_src)){
				if(imagepng($im, ROOT.AVATAR_FOLDER.'tmp/'.$file_name)){
					$err = false;
				}else{
					$err = true;
				}
			}else{
				$err = true;
			}
		}
		if(!$err){
			foreach ($avatar_image_sizes as $k => $val){
				$oke = genImageFromSource(ROOT.AVATAR_FOLDER.'tmp/'.$file_name,$all_img_name[$k],$imageInfo[0],$imageInfo[1],$val['width'],0,$aspect_ratio,false);
				if(!$oke){
					$error = 'AVATAR_'.$k.'_ERROR';
					break;
				}
			}
		}else{
			$error = 'Can not make image from source';
		}
		//xoa anh temp
		@unlink( ROOT.AVATAR_FOLDER.'tmp/'.$file_name);
	}
	if($error == ''){
		$error = $s->setSuccess($file_name);
	}else{
		$error = $s->setError($file_name);
		//xoa cac file vua tao
		foreach ($avatar_image_sizes as $k => $val){
			if(is_file($all_img_name[$k])){
				@unlink($all_img_name[$k]);
			}
		}
	}
	echo $error;
	exit();
