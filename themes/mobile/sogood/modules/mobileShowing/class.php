<?php
class mobileShowing extends Module{
	function __construct($row){
		Module::Module($row);
		require_once 'forms/showing.php';
		$this->add_form(new ShowingForm);
	}
}
