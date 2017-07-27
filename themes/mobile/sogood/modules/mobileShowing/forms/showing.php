<?php
class ShowingForm extends Form{
	function __construct(){}

	function draw(){
		global $display;

		$display->add("curPage", CGlobal::$current_page);
		$display->output("showing");
	}

}

