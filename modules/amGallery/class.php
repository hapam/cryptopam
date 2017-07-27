<?php
class amGallery extends Module {
	static function permission(){
		return array('use gallery' => "Sử dụng tool upload ảnh");
	}
    function __construct($row){
        Module::Module($row);

        $cmd = Url::getParamAdmin('cmd','');
		if($cmd == 'gallery' && User::user_access("use gallery",0,'access_denied')){
			require_once 'forms/gallery.php';
			$this->add_form(new GalleryForm());
		}
    }
}