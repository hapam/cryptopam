<?php

error_reporting ( E_ALL );
ini_set('display_errors', 1);

define('ROOT_PATH', str_replace(array('config/'),array(''),strtr(dirname( __FILE__ ) ."/",array('\\'=>'/'))));

echo 'root: '.ROOT_PATH.'<br />';

function scanDir(& $listFile, $rootDir, $dir = '', $flag = 0) {
    $aryNotRequire = array(".", "..", ".svn", "_svn","",".DS_Store");
    $hd = @opendir($rootDir . $dir);
    while (false !== ($entry = @readdir($hd))) {
        if (!in_array($entry, $aryNotRequire)) {
            if (is_dir($rootDir . $dir.$entry)) {
                echo 'DIR - <b>'.$entry.'</b><br />';
                scanDir($listFile, $rootDir, $dir.$entry.'/');
            }else{
                echo $entry.'<br />';
                if (in_array(substr($entry, -4), array('.php', 'php3', 'php4', 'php5'))) {
                    $fileName = $rootDir. $dir.$entry;
                    $tmp = explode('.', $entry);
                    $class = substr($entry, 0, strlen($entry) - (strlen($tmp[count($tmp) - 1]) + 1));
                    $listFile[$class] = $fileName;
                }
            }
        }
    }
    closedir($hd);
}

//scanDir($list, ROOT_PATH.'_cache');

$hd = opendir(ROOT_PATH.'_cache');
while (false !== ($entry = readdir($hd))) {
    echo $entry.'<br />';
}
