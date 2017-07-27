<?php
class sysConfigSite extends Module {
    static function permission(){
		return array(
			"config site" => "Cấu hình website",
			"config image" => "Cấu hình thông tin ảnh",
			"config security" => "Cấu hình bảo mật",
            "access admin page" => "Truy cập vào trang quản trị",
			"offsite mode" => "Đăng nhập khi OFF Site"
		);
	}
    function __construct($row){
        Module::Module($row);

		$cmd = Url::getParamAdmin('cmd','');
		if(($cmd == 'manage-site') && User::user_access('config site',0,'access_denied')){
			require_once 'forms/manage-site.php';
			$this->add_form(new ManageSiteForm());
		}
    }
}