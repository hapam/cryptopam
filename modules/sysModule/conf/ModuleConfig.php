<?php
class ModuleConfig{
	static $key = 'module_config';
	static function run(){
		$conf_modules = CacheLib::get(self::$key);
		if(empty($conf_modules)){
			$conf_modules = DB::fetch_all("SELECT * FROM ".T_MODULE." WHERE assign = 1 AND config = 1");
			CacheLib::set(self::$key, $conf_modules);
		}
		if(!empty($conf_modules)){
			foreach($conf_modules as $module){
				$path = DIR_MODULE;
				if(!empty($module['themes_mobile']) || !empty($module['themes'])){
					$path = DIR_THEMES . (!empty($module['themes']) ? 'website/'.$module['themes'] : 'mobile/'.$module['themes_mobile']) . '/modules/';
				}
				require_once $path.$module['name'].'/config.php';
			}
		}
	}

	static function reset(){
		CacheLib::delete(self::$key);
	}
}
