<?php
class AdminMenuForm extends Form{
	function __construct(){
		$this->region = 'leftMenu';
	}
	
	function draw(){
        global $display;

        $display->output('adminLeftMenu');
	}
}
