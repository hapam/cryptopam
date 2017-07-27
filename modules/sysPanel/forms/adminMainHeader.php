<?php
class adminMainHeaderForm extends Form{
	function __construct(){
		$this->region = 'main';
	}
	
	function draw(){
        global $display;

		$display->output('adminMainHeader');
	}
}
