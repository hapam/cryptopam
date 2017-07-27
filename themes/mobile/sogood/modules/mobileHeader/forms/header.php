<?php
class HeaderForm extends Form{
	function __construct(){
        $this->link_js(WEB_THEMES.'mobile/sogood/javascript/jquery.min.js', true);
		$this->link_js(WEB_THEMES.'mobile/sogood/javascript/effects.jquery-ui.min.js', true);
		$this->link_js(WEB_THEMES.'mobile/sogood/javascript/jquery.nivo-slider.min.js', true);
		$this->link_js(WEB_THEMES.'mobile/sogood/javascript/jquery.colorbox.min.js', true);
		$this->link_js(WEB_THEMES.'mobile/sogood/javascript/custom.js', true);
	}

	function draw(){
		global $display;

        $display->add('cur_page', CGlobal::$current_page);
        $display->add('cur_lang', Language::$activeLang);
        $display->add('langs', Language::$listLangOptions);

		//config default for all site content
		$display->add('base_url', WEB_ROOT);
		$display->add('site_name', CGlobal::$site_name);
		$display->add('site_title', CGlobal::$website_title);

		$display->output("Header");
	}

}

