<?php
class ImageHandler{
	static function genImageFromSource($src='', $des='', $des_width=0, $des_height=0, $water_mask = false){
		$imageInfo = self::image_get_info($src);
		$oke = false;
		if ($imageInfo) {
			$src_height= $imageInfo['height'];
			$src_width = $imageInfo['width'];
			$aspect_ratio = ($src_width > 0) ? ($src_height / $src_width) : 1;
	
			if ($des_height == 0 && $des_width > 0){ //chieu rong fixed, chieu cao theo ti le anh
			  $des_height = (int)round($des_width * $aspect_ratio);
			}
			elseif ($des_width == 0 && $des_height > 0){ //chieu rong theo ti le anh, chieu cao fixed
			  $des_width = (int)round($des_height / $aspect_ratio);
			}
			if($des_width == 0){$des_width = 1;} // tranh loi chia 0
			
			//Neu kich thuoc anh that nho hon kich thuoc truyen vao thi ko can co anh
			if (($des_width >= $src_width && $des_height >= $src_height) || ($des_width == 0 && $des_height == 0)) {
				$oke = self::image_resize($src, $des, $src_width, $src_height, $imageInfo);
			}else{
				//if ($aspect_ratio < $des_height / $des_width) {
				//	$des_width = (int)min($des_width, $src_width);
				//	$des_height = (int)round($des_width * $aspect_ratio);
				//}else {
				//	$des_height = (int)min($des_height, $src_height);
				//	$des_width = (int)round($des_height / $aspect_ratio);
				//}
				//if ($des_width > 0 && $des_height > 0) {
					$oke = self::image_resize($src, $des, $des_width, $des_height, $imageInfo);
				//}
			}
			if($oke && $water_mask && MASK_ACTIVE && $des_width >= MASK_MIN){
				require_once ROOT_PATH.'/includes/water_mask.class.php';
				return water_mark::makeWaterMark($des, MASK_IMG, MASK_TRANS, MASK_MARGIN, MASK_POSITION);
			}
		}
		return $oke;
	}

	static function image_get_info($file) {
	  if (!is_file($file)) {
		return FALSE;
	  }
	
	  $details = FALSE;
	  $data = @getimagesize($file);
	  $file_size = @filesize($file);
	
	  if (isset($data) && is_array($data)) {
		$extensions = array('1' => 'gif', '2' => 'jpg', '3' => 'png');
		$extension = array_key_exists($data[2], $extensions) ?  $extensions[$data[2]] : '';
		$details = array('width'     => $data[0],
						 'height'    => $data[1],
						 'extension' => $extension,
						 'file_size' => $file_size,
						 'mime_type' => $data['mime']);
	  }
	
	  return $details;
	}

	/**
	 * Verify GD2 settings (that the right version is actually installed).
	 *
	 * @return boolean
	 */
	static function image_gd_check_settings() {
	  if ($check = get_extension_funcs('gd')) {
		if (in_array('imagegd2', $check)) {
		  // GD2 support is available.
		  return TRUE;
		}
	  }
	  return FALSE;
	}

	/**
	 * Scale an image to the specified size using GD.
	 */
	static function image_resize($source, $destination, $width, $height, $info = false) {
		if (!file_exists($source)) {
		  return FALSE;
		}
		
		if($info == false){
			$info = self::image_get_info($source);
			if (!$info) {
			  return FALSE;
			}
		}
	  
		$im = self::image_gd_open($source, $info['extension']);
		if (!$im) {
		  return FALSE;
		}
	  
		$res = imagecreatetruecolor($width, $height);
		if ($info['extension'] == 'png') {
		  $transparency = imagecolorallocatealpha($res, 0, 0, 0, 127);
		  imagealphablending($res, FALSE);
		  imagefilledrectangle($res, 0, 0, $width, $height, $transparency);
		  imagealphablending($res, TRUE);
		  imagesavealpha($res, TRUE);
		}
		elseif ($info['extension'] == 'gif') {
		  // If we have a specific transparent color.
		  $transparency_index = imagecolortransparent($im);
		  if ($transparency_index >= 0) {
			// Get the original image's transparent color's RGB values.
			$transparent_color = imagecolorsforindex($im, $transparency_index);
			// Allocate the same color in the new image resource.
			$transparency_index = imagecolorallocate($res, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
			// Completely fill the background of the new image with allocated color.
			imagefill($res, 0, 0, $transparency_index);
			// Set the background color for new image to transparent.
			imagecolortransparent($res, $transparency_index);
			// Find number of colors in the images palette.
			$number_colors = imagecolorstotal($im);
			// Convert from true color to palette to fix transparency issues.
			imagetruecolortopalette($res, TRUE, $number_colors);
		  }
		}
	  
		imagecopyresampled($res, $im, 0, 0, 0, 0, $width, $height, $info['width'], $info['height']);
	  
		$result = self::image_gd_close($res, $destination, $info['extension']);
		imagedestroy($res);
		imagedestroy($im);
	
		return $result;
	}

	/**
	 * GD helper function to create an image resource from a file.
	 */
	static function image_gd_open($file, $extension) {
		$extension = str_replace('jpg', 'jpeg', $extension);
		$open_func = 'imageCreateFrom'. $extension;
		if (!function_exists($open_func)) {
		  return FALSE;
		}
		
		//dungbt add to fix bug
		return @$open_func($file);
	}

	/**
	 * GD helper to write an image resource to a destination file.
	 */
	static function image_gd_close($res, $destination, $extension) {
		$extension = str_replace('jpg', 'jpeg', $extension);
		$close_func = 'image'. $extension;
		if (!function_exists($close_func)) {
		  return FALSE;
		}
		if ($extension == 'jpeg') {
		  return $close_func($res, $destination, IMG_QUALITY);
		}
		else {
		  return $close_func($res, $destination);
		}
	}
}
