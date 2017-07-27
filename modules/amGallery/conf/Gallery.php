<?php
class Gallery{
	static $min_sort = 10000;
	static $stepNew  = 40;
	static $stepNext = 4;
	static $defCatID = 1;
	static $maxItemPerDirect = 1000;
	static function getImageGallery($img = '', $time = 0, $size = 0){
		return ImageUrl::getImageURL($img, $time, $size, GALLERY_KEY, GALLERY_FOLDER);
	}
	static function addMultiUploadCore($objectForm, $fileConfig = ''){
		if($fileConfig != ''){
			$html5 = ConfigSite::getConfigFromDB('multiupload', '', false, 'site_configs');
			$key   = $html5 ? 'five' : 'fy';
			$dir   = "modules/amGallery/js/uploadi$key/";
			$objectForm->link_css($dir."uploadi$key.css");
			$objectForm->link_js($dir."jquery.uploadi$key.min.js");
			$objectForm->link_js($dir.$fileConfig);
		}
	}
	static function getSortInsert(){
		$ok = DB::fetch("SELECT MIN(sort) as min_sort, id FROM ".T_GALLERY);
		if($ok){
			if($ok['id'] > 0){
				return $ok['min_sort'] - self::$stepNew;
			}
		}
		return self::$min_sort;
	}
}