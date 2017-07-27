<?php
class Layout{
	static $current = false,$page = false,$page_cache_file='',$extraHeader = '',$extraFooter = '',$extraHeaderCSS = '',$extraHeaderJS = '',$extraFooterJS = '';
	static function Run(){
		FileHandler::CheckDir(PAGE_CACHE_DIR);

		//page name
		$page_name = CGlobal::$current_page;
        $page_name_file = $page_name;

		//neu la phien ban mobile thi them hau to vao de phan biet
        if(!isset(CGlobal::$corePages[$page_name])){
            if(CGlobal::$mobile[0] && CGlobal::$configs['themes_mobile'] != 'no_mobile'){
                $page_name_file = 'mobile_'.CGlobal::$configs['themes_mobile'].'_'.$page_name_file;
            }elseif(CGlobal::$configs['themes'] != 'sys'){
                $page_name_file = CGlobal::$configs['themes'].'_'.$page_name_file;
            }
        }

		//luu file cache page
		Layout::$page_cache_file = PAGE_CACHE_DIR.$page_name_file.'.php';

		$refresh_page = Url::getParamInt('refresh_page', 0);
		if($refresh_page == 1){
			self::del_page_cache($page_name_file);
		}

		if($refresh_page != 1 && (PAGE_CACHE == 1) && file_exists(Layout::$page_cache_file)){
			require_once Layout::$page_cache_file;
		}
		else{
			if(!empty(CGlobal::$arrPage)){
				Layout::$page = isset(CGlobal::$arrPage[$page_name]) ? CGlobal::$arrPage[$page_name] : false;
			}
			if(!Layout::$page){
				$re = DB::query('SELECT id, name, title, layout, themes, themes_mobile, layout_mobile FROM '.T_PAGE.' WHERE name="'.addslashes($page_name).'"');
				if($re){
					Layout::$page = mysql_fetch_assoc($re);
				}
			}
			if(!Layout::$page){
				Url::redirect_url(WEB_ROOT);
			}
			LayoutGen::PageGenerate();
		}
	}

	static function update_page($ids){//$ids là danh sách id dạng "1,2,3";
		$re = DB::query('SELECT name FROM '.T_PAGE.' WHERE id IN ('.$ids.')');
		$pages = array();
		if($re){
			while ($page = mysql_fetch_assoc($re)){
				if($page && $page['name']){
					self::del_page_cache($page['name']);
				}
			}
		}
		return true;
	}
	static function update_all_page(){
		$re = DB::query('SELECT name FROM '.T_PAGE);
		$pages = array();
		if($re){
			while ($page = mysql_fetch_assoc($re)){
				if($page && $page['name']){
					self::del_page_cache($page['name'], false);
				}
			}
			//xoa cache moithem vao 12/6/2012
			FunctionLib::empty_all_dir(DIR_CACHE,true,true);
			require(ROOT_PATH.'tools/delcache.php');
		}
		return true;
	}
	
	static function add_page_cache($page = '', $content=''){
		if(($page != '') && ($fp = @fopen($page, 'w+'))){
			fwrite ($fp, $content );
			fclose($fp);
			chmod(Layout::$page_cache_file,0777);
			return true;
		}
		return false;
	}
	
	static function del_page_cache($page='', $delAll = true){
		if($page != ''){
			@unlink(PAGE_CACHE_DIR.$page.'.php');
			//xoa cache moithem vao 12/6/2012
			if($delAll){
				FunctionLib::empty_all_dir(DIR_CACHE,true,true);
				require(ROOT_PATH.'tools/delcache.php');
			}
			return true;
  		}
		return false;
	}
}
