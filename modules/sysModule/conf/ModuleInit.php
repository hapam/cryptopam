<?php
class ModuleInit{
	static $key = 'module_init';
	static function run(){
		$init_modules = CacheLib::get(self::$key);
		if(empty($init_modules)){
			$init_modules = DB::fetch_all("SELECT * FROM ".T_MODULE." WHERE assign = 1 AND init = 1");
			CacheLib::set(self::$key, $init_modules);
		}
		if(!empty($init_modules)){
			foreach($init_modules as $module){
				$path = DIR_MODULE;
				if(!empty($module['themes_mobile']) || !empty($module['themes'])){
					$path = DIR_THEMES . (!empty($module['themes']) ? 'website/'.$module['themes'] : 'mobile/'.$module['themes_mobile']) . '/modules/';
				}
				require_once $path.$module['name'].'/init.php';

				//for debug only
				if(DEBUG){
					$debug = "<b style='color:red'>".$module['name']."</b>";
					Module::$init[] = $debug;
				}
			}
		}
	}
	static function reset(){
		CacheLib::delete(self::$key);
	}
}
