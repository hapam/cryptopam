<?php
function genImageFromSource($src='', $des='', $src_width=0, $src_height=0, $des_width=0, $des_height=0, $aspect_ratio=1, $water_mask=true){
	//ti le dang la height/width
	$oke = false;
	if (empty($des_height) && !empty($des_width)) { //chieu rong fixed, chieu cao theo ti le anh
      $des_height = (int)round($des_width * $aspect_ratio);
    }elseif (empty($des_width) && !empty($des_height)) { //chieu rong theo ti le anh, chieu cao fixed
      $des_width = (int)round($des_height / $aspect_ratio);
    }
    if(($des_width >= $src_width && $des_height >= $src_height) || ($des_height == 0 && $des_width == 0)) {
		$oke = image_resize($src, $des, $src_width, $src_height);
	}else{
		$oke = image_resize($src, $des, $des_width, $des_height);
	}
	if($oke && $water_mask && MASK_ACTIVE && $des_width >= MASK_MIN){
		require_once 'water_mask.class.php';
		return water_mark::makeWaterMark($des, MASK_IMG, MASK_TRANS, MASK_MARGIN, MASK_POSITION);
	}
	return $oke;
}

function image_get_info($file) {
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
function image_gd_check_settings() {
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
function image_resize($source, $destination, $width, $height) {
  if (!file_exists($source)) {
    return FALSE;
  }

  $info = image_get_info($source);
  if (!$info) {
    return FALSE;
  }

  $im = image_gd_open($source, $info['extension']);
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

  $result = image_gd_close($res, $destination, $info['extension']);
  imagedestroy($res);
  imagedestroy($im);


  return $result;
}

/**
 * Rotate an image the given number of degrees.
 */
function image_rotate($source, $destination, $degrees, $bg_color = 0) {
  if (!function_exists('imageRotate')) {
    return "NO_FUNCTION";
  }

  $info = image_get_info($source);
  if (!$info) {
	return "FILE_ERR";
  }

  $im = image_gd_open($source, $info['extension']);
  if (!$im) {
    return "GD_ERR";
  }

  $res = imageRotate($im, $degrees, $bg_color);
  $result = image_gd_close($res, $destination, $info['extension']);

  return 0;
}

/**
 * Crop an image using the GD toolkit.
 */
function image_crop($source, $destination, $x, $y, $width, $height) {
  $info = image_get_info($source);
  if (!$info) {
	return FALSE;
  }

  $im = image_gd_open($source, $info['extension']);
  $res = imageCreateTrueColor($width, $height);
  imageCopy($res, $im, 0, 0, $x, $y, $width, $height);
  $result = image_gd_close($res, $destination, $info['extension']);

  imageDestroy($res);
  imageDestroy($im);

  return $result;
}

/**
 * GD helper function to create an image resource from a file.
 */
function image_gd_open($file, $extension) {
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
function image_gd_close($res, $destination, $extension) {
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
