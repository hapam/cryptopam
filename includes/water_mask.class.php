<?php

define("WATER_MASK_POSITION", "bottomright");
define("WATER_MASK_IMG", ROOT_PATH . IMAGE_PATH_STATIC . "code/default_img/water_mask.gif");
	
class water_mark{
	static function makeWaterMark($des_image, $source_file = '', $transparency = 30, $margin = 5, $pos = ''){
		$source_file = ImageUrl::getWaterMask($source_file);

		$sourceInfo = @getimagesize($source_file);
		$desInfo 	= @getimagesize($des_image);

		$oke = false;
		if (strtolower($desInfo["mime"]) != "image/gif") {
			$source_file =	self::image_get_from_source($source_file, $sourceInfo);
			$jpegImg 	 =	self::image_get_from_source($des_image, $desInfo);

			$pos = $pos == '' ? WATER_MASK_POSITION : $pos;
			$arrPos = self::getPositionWatermark($desInfo, $sourceInfo, $pos, $margin);
			$wmX = $arrPos['x'];
			$wmY = $arrPos['y'];

			imagecopy($jpegImg, $source_file, $wmX, $wmY, 0, 0, $sourceInfo[0], $sourceInfo[1]);

			$jpegQuality = 100;
			$oke = self::gen_image_watermark($jpegImg, $des_image, $jpegQuality, $desInfo);	

			imagedestroy($jpegImg);
			imagedestroy($source_file);
//			imagedestroy($des_image);
		}
		return $oke;
	}	
	//	LẤY VỊ TRÍ CỦA WATERMARK TRÊN ẢNH
	static function getPositionWatermark($desInfo, $sourceInfo, $pos, $margin) {
		switch ($pos) {
			case "center":
				$x =  ($desInfo[0] - $sourceInfo[0])/2;
				$y =  ($desInfo[1] - $sourceInfo[1])/2;
				break;
				
			case "bottomright":
				$x =  $desInfo[0] - $sourceInfo[0] - $margin;
				$y =  $desInfo[1] - $sourceInfo[1] - $margin;
				break;
				
			case "topright":
				$x =  $desInfo[0] - $sourceInfo[0] - $margin;
				$y =  $margin;
				break;
				
			case "topleft":
				$x =  $margin;
				$y =  $margin;
				break;
				
			default:
			case "bottomleft":
				$x =  $margin;
				$y =  $desInfo[1] - $sourceInfo[1] - $margin;
				break;
		}
		return array('x' => $x, 'y' => $y);
	}
	
	/* water mark anh */
	static function image_get_from_source($fileimg, $imgInfo) {
		$source = '';
		switch(strtolower($imgInfo["mime"])) {
			case "image/jpg":
			case "image/jpeg":
				$source = imagecreatefromjpeg($fileimg);
				break;
			case "image/gif":
				$source = imagecreatefromgif($fileimg);
				break;
			case "image/png":
				$source = imagecreatefrompng($fileimg);
				break;
	    }
	    return $source;
	}
	
	// DUC-NH: GEN ẢNH VỚI WATERMARK
	static function gen_image_watermark($jpegImg, $des_image, $jpegQuality=100, $desInfo){
		$oke = false;
		switch(strtolower($desInfo["mime"])) {
			case "image/jpg":
			case "image/jpeg":
				$oke = imagejpeg($jpegImg, $des_image, $jpegQuality);
				break;
			case "image/gif":
				$oke = imagegif($jpegImg, $des_image);
				break;
			case "image/png":
				$oke = imagepng($jpegImg, $des_image);
				break;
		}
		return $oke;
	}
	static function test_water_mark($img_link){
		$path_default = WEB_ROOT.'style/images/water_mark/mc.png';
		return image_water_mark($img_link, $path_default);
	}
}