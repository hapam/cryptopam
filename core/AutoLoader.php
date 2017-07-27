<?php
define('AUTOLOAD_CACHE_FILE', DIR_CACHE . '_autoload.php');

function __autoload($className) {
	if (class_exists($className, false) || interface_exists($className, false)) {
		return;
	}

	if (!is_file(AUTOLOAD_CACHE_FILE)) {
		if(!AutoLoader::readDir()){
			echo '<em>'.DIR_CACHE.'</em> is not existed!!!';
			exit();
		}
	}

    require (AUTOLOAD_CACHE_FILE);

    if (isset($autoload) && is_array($autoload) && isset($autoload[$className]) && file_exists($autoload[$className])) {
        require_once($autoload[$className]);
        array_push(AutoLoader::$list_file_loaded, $autoload[$className]);
    }
    else {
        echo 'Not found class {'.$className.'}';
        echo '<pre style="text-align: left">'; print_r(debug_backtrace()); echo '</pre>';
        exit;
    }
}


class AutoLoader {	
	static $list_file_loaded = array();

	/**
	 * read dir
	 *
	 */
	static public function readDir($flag = 0) {
        //danh sach file thu muc core
		$listCoreFiles = array();
		self::scanDir($listCoreFiles, ROOT_PATH .'core/');

        //danh sach file ajax
        $listAjaxFiles = array();
        self::scanDir($listAjaxFiles, ROOT_PATH .'includes/ajax_action/');
		
		//phpmailer
        $listMailerFiles = array();
        self::scanDir($listMailerFiles, ROOT_PATH .'includes/mailer/lib/');

		//ajax + core from modules website, mobile
		$listMobileModuleAjaxFiles = array();
        self::scanDir2($listMobileModuleAjaxFiles, ROOT_PATH .'themes/mobile/');
		$listWebsiteModuleAjaxFiles = array();
        self::scanDir2($listWebsiteModuleAjaxFiles, ROOT_PATH .'themes/website/');

		//ajax + core from modules core
		$listCoreModuleAjaxFiles = array();
        self::scanDir3($listCoreModuleAjaxFiles, ROOT_PATH .'modules/');

        //ghi file
        $listFile = $listCoreFiles + $listAjaxFiles + $listMailerFiles + $listMobileModuleAjaxFiles + $listWebsiteModuleAjaxFiles + $listCoreModuleAjaxFiles;
		return self::writeFile($listFile);
	}

	/**
	 * scan directory, get list file in directory
	 *
	 * @param array $listFile
	 * @param string $rootDir
	 * @param string $dir
	 */
	static public function scanDir(& $listFile, $rootDir, $dir = '', $flag = 0) {
		$aryNotRequire = array(".", "..", ".svn", "_svn","",".DS_Store");
		$hd = @opendir($rootDir . $dir);
		while (false !== ($entry = @readdir($hd))) {
			if (!in_array($entry, $aryNotRequire)) {
				if (is_file($rootDir . $dir . $entry) && in_array(substr($entry, -4), array('.php', 'php3', 'php4', 'php5'))) {
					$fileName = $rootDir. $dir.$entry;
					$tmp = explode('.', $entry);
					$class = substr($entry, 0, strlen($entry) - (strlen($tmp[count($tmp) - 1]) + 1));
					$listFile[$class] = $fileName;
				}
				if (is_dir($rootDir . $dir.$entry)) {
					self::scanDir($listFile, $rootDir, $dir.$entry.'/');
				}
			}
		}
		closedir($hd);
	}
	
	static public function scanDir2(& $listFile, $rootDir) {
		$theme_dirs = @scandir($rootDir);
		unset($theme_dirs[0]);
		unset($theme_dirs[1]);
        if(isset($theme_dirs[2]) && $theme_dirs[2] == '.DS_Store'){
            unset($theme_dirs[2]);
        }
		$aryNotRequire = array(".", "..", ".svn", "_svn","",".DS_Store");
		if(!empty($theme_dirs)){
			foreach($theme_dirs as $dir){
				$listModules = @scandir($rootDir . $dir . '/modules');
				unset($listModules[0]);
				unset($listModules[1]);
				if(isset($listModules[2]) && $listModules[2] == '.DS_Store'){
					unset($listModules[2]);
				}
				foreach($listModules as $d){
					$hd = @opendir($rootDir . $dir . '/modules/' . $d . '/ajax');
					if($hd){
						while (false !== ($entry = @readdir($hd))) {
							if (!in_array($entry, $aryNotRequire)) {
								$fileName = $rootDir . $dir . '/modules/' . $d . '/ajax/' . $entry;
								if (is_file($fileName) && in_array(substr($entry, -4), array('.php', 'php3', 'php4', 'php5'))) {
									$tmp = explode('.', $entry);
									$class = substr($entry, 0, strlen($entry) - (strlen($tmp[count($tmp) - 1]) + 1));
									$listFile[$class] = $fileName;
								}
							}
						}
						closedir($hd);
					}
					$hd = @opendir($rootDir . $dir . '/modules/' . $d . '/conf');
					if($hd){
						while (false !== ($entry = @readdir($hd))) {
							if (!in_array($entry, $aryNotRequire)) {
								$fileName = $rootDir . $dir . '/modules/' . $d . '/conf/' . $entry;
								if (is_file($fileName) && in_array(substr($entry, -4), array('.php', 'php3', 'php4', 'php5'))) {
									$tmp = explode('.', $entry);
									$class = substr($entry, 0, strlen($entry) - (strlen($tmp[count($tmp) - 1]) + 1));
									$listFile[$class] = $fileName;
								}
							}
						}
						closedir($hd);
					}
				}
			}
		}
	}
	
	static public function scanDir3(& $listFile, $rootDir) {
		$aryNotRequire = array(".", "..", ".svn", "_svn","",".DS_Store");
		$listModules = @scandir($rootDir);
		unset($listModules[0]);
		unset($listModules[1]);
		if(isset($listModules[2]) && $listModules[2] == '.DS_Store'){
			unset($listModules[2]);
		}
		foreach($listModules as $d){
			$hd = @opendir($rootDir . $d . '/ajax');
			if($hd){
				while (false !== ($entry = @readdir($hd))) {
					if (!in_array($entry, $aryNotRequire)) {
						$fileName = $rootDir . $d . '/ajax/' . $entry;
						if (is_file($fileName) && in_array(substr($entry, -4), array('.php', 'php3', 'php4', 'php5'))) {
							$tmp = explode('.', $entry);
							$class = substr($entry, 0, strlen($entry) - (strlen($tmp[count($tmp) - 1]) + 1));
							$listFile[$class] = $fileName;
						}
					}
				}
				closedir($hd);
			}
			$hd = @opendir($rootDir . $d . '/conf');
			if($hd){
				while (false !== ($entry = @readdir($hd))) {
					if (!in_array($entry, $aryNotRequire)) {
						$fileName = $rootDir . $d . '/conf/' . $entry;
						if (is_file($fileName) && in_array(substr($entry, -4), array('.php', 'php3', 'php4', 'php5'))) {
							$tmp = explode('.', $entry);
							$class = substr($entry, 0, strlen($entry) - (strlen($tmp[count($tmp) - 1]) + 1));
							$listFile[$class] = $fileName;
						}
					}
				}
				closedir($hd);
			}
		}
	}

	static public function writeFile(& $listFile) {
		$cacheFile = AUTOLOAD_CACHE_FILE;
		if (!is_dir(DIR_CACHE)){
			if(!@mkdir(DIR_CACHE,0777,true)){
				return false;
			}
		}
		ob_start();
		var_export($listFile);
		$cacheContents = ob_get_clean();
		try {
			$handle = @fopen($cacheFile, "w");
			if ($handle) {
				fwrite($handle, "<?php\n\n");
				fwrite($handle, "\$autoload = ");
				fwrite($handle, $cacheContents);
				fwrite($handle, ";\n\n");
				fclose($handle);
			}
		} catch(Exception $e) {
			show($e->getMessage());
			exit;
		}
		return true;
	}

}
