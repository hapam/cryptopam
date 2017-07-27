<?php
class AdminMenuInfoForm extends Form{
	function __construct(){
		$this->region = 'leftInfo';
	}
	
	function draw(){
        global $display;
		
		$themes = array(
			"red" => 
				"Red",
			"pink" => 
				"Pink",
			"purple" => 
				"Purple",
			
			"deep-purple" => 
				"Deep Purple",
			
			"indigo" => 
				"Indigo",
			
			"blue" => 
				"Blue",
			
			"light-blue" => 
				"Light Blue",
			
			"cyan" => 
				"Cyan",
			
			"teal" => 
				"Teal",
			
			"green" => 
				"Green",
			
			"light-green" => 
				"Light Green",
			
			"lime" => 
				"Lime",
			
			"yellow" => 
				"Yellow",
			
			"amber" => 
				"Amber",
			
			"orange" => 
				"Orange",
			
			"deep-orange" => 
				"Deep Orange",
			
			"brown" => 
				"Brown",
			
			"grey" => 
				"Grey",
			
			"blue-grey" => 
				"Blue Grey",
			
			"black" => 
				"Black",
			
		);
		
		$display->add('themes', $themes);
		$display->add('theme_default', CookieLib::get_cookie('theme-admin', 'cyan'));
		$display->add('admin_config', @unserialize(CGlobal::$configs['admin_config']));
		$display->add('debug_mode', DEBUG);
		$display->add('edit_mode', RootPanel::isEditMode());
		$display->add('is_root', User::is_root());
		$display->output('adminLeftMenuInfo');
	}
}
