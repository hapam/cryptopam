<?php
class LayoutGen{
    static $blocks = array();

    static function PageGenerate(){
        $code = self::TextGenerate();
        if((PAGE_CACHE == 1) && Layout::add_page_cache(Layout::$page_cache_file, $code)){
            require_once Layout::$page_cache_file;
        }else{
            eval('?>'.$code.'<?php ');
        }
    }

    static function TextGenerate(){
        $code ='<?php'."\n";
        $code .='Layout::$page = '.str_replace(array("\n",' '),array('',''),var_export(Layout::$page,true)).';';
        $code.="\n".'$blocks = ';

        $re = DB::query("SELECT id, module_id, region, mobile FROM  ".T_BLOCK." WHERE  page_id= ".Layout::$page['id']." ORDER BY position");
        if($re){
            while ($block = mysql_fetch_assoc($re)){
                if(CGlobal::$mobile[0] && CGlobal::$configs['themes_mobile'] != '' && CGlobal::$configs['themes_mobile'] != 'no_mobile'){
                    if($block['mobile'] == 1){
                        self::$blocks[$block['id']] = $block;
                    }
                }elseif($block['mobile'] == 0){
                    self::$blocks[$block['id']] = $block;
                }
            }
        }
        $mids = "";
        foreach(self::$blocks as $id=>$block){
            $mids .= ($mids!="" ? "," : "").$block['module_id'];
            self::$blocks[$id]['module'] = array();
        }
        if($mids != ""){
            $re = DB::query("SELECT id, name, themes, themes_mobile FROM ".T_MODULE." WHERE id IN($mids)");
            if($re){
                $unsetModule = array();
                $b_modules = array();
                while($b_module = mysql_fetch_assoc($re)){
                    if(CGlobal::$mobile[0] && (CGlobal::$configs['themes_mobile'] != 'no_mobile')){
                        if($b_module['themes_mobile'] != '' && $b_module['themes_mobile'] != CGlobal::$configs['themes_mobile']){
                            $unsetModule[$b_module['id']] = $b_module['id'];
                            continue;
                        }
                    }
                    if((CGlobal::$configs['themes'] != 'sys') && ($b_module['themes'] != '') && ($b_module['themes'] != CGlobal::$configs['themes'])){
                        $unsetModule[$b_module['id']] = $b_module['id'];
                        continue;
                    }
                    $b_modules[$b_module['id']] = $b_module;
                }
                if($b_modules){
                    foreach(self::$blocks as $id=>$block){
                        if(isset($b_modules[$block['module_id']])){
                            self::$blocks[$id]['module'] = $b_modules[$block['module_id']];
                        }elseif(isset($unsetModule[$block['module_id']])){
                            unset(self::$blocks[$id]);
                        }
                    }
                }
            }
        }

        $code .= str_replace(array("\n",' '),array('',''),var_export(self::$blocks,true)).';';
        $code .='
			foreach($blocks as &$block){
				if(!empty($block["module"])){
					$dir_module	= DIR_MODULE;
					if(isset($block["module"]["themes"]) || isset($block["module"]["themes_mobile"])){
						if($block["module"]["themes"] != ""){
							$dir_module = DIR_THEMES."website/".$block["module"]["themes"]."/modules/";
						}elseif($block["module"]["themes_mobile"] != ""){
							$dir_module = DIR_THEMES."mobile/".$block["module"]["themes_mobile"]."/modules/";
						}
					}
					$file_moule = $dir_module.$block["module"]["name"]."/class.php";
					if(file_exists($file_moule)){
						if(DEBUG) {
							$start_block = microtime(true);
						}
						require_once $file_moule;
						$block["object"] = new $block["module"]["name"]($block);
						if(isset($_POST["form_block_id"]) && ($_POST["form_block_id"] == $block["id"])){
							if (CGlobal::$tokenData !== Url::getParam(TOKEN_KEY_NAME)) {
								exit("invalid token");
							}
							$block["object"]->submit();
						}
						if(DEBUG) {
							if(!isset(CGlobal::$arrModuleDebug[$block["module"]["name"]])){
								CGlobal::$arrModuleDebug[$block["module"]["name"]] = array("class" => 0, "draw"  => 0, "form" => array());
							}
							CGlobal::$arrModuleDebug[$block["module"]["name"]]["class"] += microtime(true) - $start_block;
						}
					}
				}
			}
			if(Url::isAdminUrl()){
			    require_once ROOT_PATH."core/PageBeginAdmin.php";
			}
			else{
			    require_once ROOT_PATH."core/PageBegin.php";
			}
		?>';
        $layout_file = ROOT_PATH;
        if(!isset(CGlobal::$corePages[Layout::$page['name']])){
            $layout_file = DIR_THEMES;
            if(CGlobal::$mobile[0] && CGlobal::$configs['themes_mobile'] != 'no_mobile'){
                $layout_file .= 'mobile/'.CGlobal::$configs['themes_mobile'].'/'.Layout::$page['layout_mobile'];
            }else{
                $layout_file .= 'website/'.CGlobal::$configs['themes'].'/'.Layout::$page['layout'];
            }
        }else{
            $layout_file .= Layout::$page['layout'];
        }
        $text = file_get_contents($layout_file);
        while(($pos=strpos($text,'[[|'))!==false){
            $code .= substr($text, 0,  $pos);
            $text = substr($text, $pos+3,  strlen($text)-$pos-3);
            if(preg_match('/([^\|]*)/',$text, $match)){
                if(isset($match[1])){
                    $code .= self::RegionGenerate($match[1]);
                }
                if(($pos = strpos($text,'|]]',0))!==false){
                    $text = substr($text, $pos+3,  strlen($text)-$pos-3);
                }
            }
            else{
                break;
            }
        }
        $code .= $text;
        $code .= "\n<?php if(Url::isAdminUrl()){require_once ROOT_PATH.'core/PageEndAdmin.php';}else{require_once ROOT_PATH.'core/PageEnd.php';} ?>";
        return $code;
    }

    static function RegionGenerate($region){
        $code = '';
        foreach(self::$blocks as $id=>$block){
            if($block['region'] == $region){
                $code .= '<?php
					if(isset($blocks['.$id.']["object"])){
						if(DEBUG) {
							$start_block = microtime(true);
						}
						$blocks['.$id.']["object"]->on_draw($blocks['.$id.']["region"]);
						if(DEBUG) {
							CGlobal::$arrModuleDebug[$blocks['.$id.']["object"]->data["module"]["name"]]["draw"] += (microtime(true) - $start_block);
						}
					}
				?>';
            }
        }
        return $code;
    }
}
