<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {literal}{{/literal}
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
{literal}}{/literal}

class ajax_{$mod_name} {literal}{{/literal}
	function playme() {literal}{{/literal}
		$code = Url::getParam('code');
		switch( $code ) {literal}{{/literal}
			case 'test':
				$this->Test();
				break;
			default: $this->home();
		{literal}}{/literal}
	{literal}}{/literal}
	function Test() {literal}{{/literal}
		FunctionLib::JsonSuccess('Hello World !!!',array('say' => 'Yeah !!!'), true);
	{literal}}{/literal}
	function home() {literal}{{/literal}
		die("Nothing to do...");
	{literal}}{/literal}
{literal}}{/literal}