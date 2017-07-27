<?php
class HeaderForm extends Form{
	function __construct(){
		if(!User::is_login()){
			Url::redirect('login');
		}
		$this->link_css(FunctionLib::getPathThemes().'style/bootstrap.min.css', true);
		$this->link_css(FunctionLib::getPathThemes().'style/bootstrap-theme.min.css', true);
		$this->link_js(FunctionLib::getPathThemes().'javascript/bootstrap.min.js', true);
		$this->link_js(FunctionLib::getPathThemes().'javascript/function_lib.js', true);
	}

	function draw(){
		global $display;

        $is_mobile = mobile_device_detect();
		//Menu
        $page = CGlobal::$current_page;
        $menu = Menu::getMenu(1);
        foreach ($menu as $k => $v) {
			$v['link'] = $v['link'] == '' ? 'home' : $v['link'];
            if ($menu[$k]['parent'] == 0 && ($v['link'] == $page || stripos($v['link'], $page . '/') !== false || stripos($v['link'], $page . '.html') !== false)) {
                $menu[$k]['active'] = true;
                break;
            }
        }

		$display->add('menu', $menu);
		$display->add('user', User::$current->data);
		$display->add('logout', Url::build('login', array(), '?signout=1'));
		$display->add('config', Url::build('config'));
		$display->add('cur_page', CGlobal::$current_page);
        $display->add('cur_lang', Language::$activeLang);
        $display->add('langs', Language::$listLangOptions);

        //chi cho phep chon option nay khi dang vao web bang mobile
        $display->add('canSetMode', is_array($is_mobile) && $is_mobile[0] == true && CookieLib::get_cookie('websiteMode','') != '');

		//config default for all site content
		$display->add('base_url', WEB_ROOT);
		$display->add('site_name', CGlobal::$site_name);
		$display->add('site_title', CGlobal::$website_title);
		$display->add('logo', CGlobal::$logo);
        $display->add('logo_size', CGlobal::$logo_size);
		$display->add('logo_title', CGlobal::$logo_title != '' ? CGlobal::$logo_title : '');
		$display->add('blank_image', 'style/images/blank.gif');

		$display->output("Header");
	}

}

