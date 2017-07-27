<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_category {
    function playme(){
		$code = Url::getParam('code');
		switch( $code ){
			case 'load-cat':
                $this->loadCategory();
                break;
			default: $this->home();
		}
    }
	function loadCategory() {
        $type = Url::getParamInt('type', -1);
        if ($type != -1) {
            $data = Category::optCategory(0, $type, $def);
            FunctionLib::JsonSuccess('success', array('data' => $data), true);
        }
        FunctionLib::JsonErr('Lỗi! Sai dữ liệu', false, true);
    }
    function home(){
        global $display;
        die("Nothing to do...");
    }
}//class
