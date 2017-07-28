<?php

error_reporting ( E_ALL );
ini_set('display_errors', 1);

define('ROOT_PATH', str_replace(array('config/'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));

echo 'root: '.ROOT_PATH.'<br />';

$dir = ROOT_PATH."_cache";

// Open a known directory, and proceed to read its contents
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
        }
        closedir($dh);
    }else{
        echo 'Ko mo duoc thu muc';
    }
}else{
    echo 'Ko ton tai thu muc';
}
