<?php
class StaticPageForm extends Form{
    var $page;
    function __construct(){
        $page_url = Url::getParam('url');
        $this->page = StaticP::getPageContent($page_url);
		
		if(empty($this->page)){
			Url::redirect(CGlobal::$defaultHomePage);
		}
		CGlobal::$website_title = $this->page['title'] . ' - ' . CGlobal::$website_title;
    }

    function draw(){
		global $display;

		FunctionLib::addBreadcrumb('Trang chủ', WEB_ROOT);
		FunctionLib::addBreadcrumb($this->page['title']);
		$breadcum = FunctionLib::getBreadcrumb();

		$display->add('breadcum', $breadcum);
		$display->add("title", $this->page['title']);
		$display->add("html", StringLib::post_db_parse_html($this->page['content']));
		$display->output("staticPage");
    }
}
