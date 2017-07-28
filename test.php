<?php

error_reporting ( E_ALL );
ini_set('display_errors', 1);

define('ROOT_PATH', str_replace(array('config/'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));

echo 'root: '.ROOT_PATH.'<br />';

// Desired folder structure
$structure = './depth1/depth2/depth3/';

// To create the nested structure, the $recursive parameter 
// to mkdir() must be specified.

if (!mkdir($structure, 0777, true)) {
  die('Failed to create folders...');
}

