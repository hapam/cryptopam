<?php
ConfigSite::addModuleConfig('time_store', 1, array(
	'per' 	=>	User::user_access('edit myCoin'),
	'title'	=>	'myCoin - Số ngày lưu',
	'type' => 'text',
    'number' => true,
    'ext' => array(
        'onkeypress' => "return shop.numberOnly(this, event)",
        'maxlength' => 1
    )
));

ConfigSite::addModuleConfig('time_load', 5, array(
	'per' 	=>	User::user_access('edit myCoin'),
	'title'	=>	'myCoin - Thời gian load lại dữ liệu',
	'type' => 'text',
    'number' => true,
    'ext' => array(
        'onkeypress' => "return shop.numberOnly(this, event)",
        'maxlength' => 3
    )
));