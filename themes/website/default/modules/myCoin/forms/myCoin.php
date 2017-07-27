<?php
class myCoinForm extends Form{
	function __construct(){
	}

	function draw(){
		global $display;

		$display->add('hello', '== TYPE CODE HERE ==');
		$display->output('myCoin');
	}
}