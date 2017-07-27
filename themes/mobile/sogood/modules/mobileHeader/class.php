<?php
class mobileHeader extends Module{
	function __construct($row){
		Module::Module($row);
		require_once 'forms/header.php';
		$this->add_form(new HeaderForm);
	}
}
