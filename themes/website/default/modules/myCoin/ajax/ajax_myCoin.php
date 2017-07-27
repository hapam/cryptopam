<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_myCoin {
	function playme() {
		$code = Url::getParam('code');
		switch( $code ) {
			case 'test':
				$this->Test();
				break;
			default: $this->home();
		}
	}
	function Test() {
		FunctionLib::JsonSuccess('Hello World !!!',array('say' => 'Yeah !!!'), true);
	}
	function home() {
		die("Nothing to do...");
	}
}