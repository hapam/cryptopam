<?php
ConfigSite::addModuleConfig('try_export', 0, array(
	'per' 	=>	User::user_access('admin'),
	'label'	=>	'AdminPanel - Test Upload',
	'type'	=>	'checkbox',
    'style' =>  'onoff',
    'label_pos' => 'left'
));