<?php
class Footer extends Module{
	function __construct($row){
		Module::Module($row);
		require_once 'forms/footer.php';
		$this->add_form(new FooterForm);
	}
}
