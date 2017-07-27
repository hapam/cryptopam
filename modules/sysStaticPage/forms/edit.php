<?php
class editStaticPageForm extends Form {
	var $page, $key = 'static_page_key';

	function __construct() {
		parent::__construct();
		StaticP::getPageList();
		
		$page_url = Url::getParam('id');
        $lang = Url::getParam('lang', '');
        $pid = Url::getParam('pid', '');
		$action = Url::getParamAdmin('action');
		$pageListKey = isset(CGlobal::$configs[$this->key]) ? unserialize(CGlobal::$configs[$this->key]) : array();

        if($lang != ''){
            $page_url = $pid.'-'.$lang;
        }
		if($page_url != '' && !empty($pageListKey) && isset(CGlobal::$configs[$page_url])){
			$this->page = unserialize(CGlobal::$configs[$page_url]);
            $this->page['pid'] = $pid;
            $this->page['lang'] = $lang;
			$this->page['is_mobile'] = stripos($this->page['url'], 'mobile-') !== false;

			//css link
			$this->page['css'] = 'website/'.CGlobal::$configs['themes'];
			if($this->page['is_mobile'] && CGlobal::$configs['themes_mobile'] != 'no_mobile'){
				$this->page['css'] = 'mobile/'.CGlobal::$configs['themes_mobile'];
			}
			$this->page['css'] = WEB_THEMES . $this->page['css'] . '/style/style_edit.css';
		}

		//ko tim thay khi edit
		if($action != '' && $action != 'add' && empty($this->page)){
			Url::redirect('admin', array('trang-tinh'));
		}

		if($action == 'del'){
			//xoa key trong mang, xoa cac trang da dc dich
            $need_del = array($page_url);
            foreach(Language::$listLang as $k => $v){
                $key = $page_url.'-'.$k;
                if(isset($pageListKey[$key])){
                    $need_del[] = $key;
                    unset($pageListKey[$key]);
                }
            }
            unset($pageListKey[$page_url]);
            ConfigSite::setConfigToDB($this->key, serialize($pageListKey));

			//xoa noi dung trang
			DB::delete(T_CONFIGS, "conf_key IN ('".implode("','", $need_del)."')");

			//delete cached
			StaticP::clearCacheStaticPage($url);

			Url::redirect('admin', array('cmd' => 'trang-tinh'));
		}
	}
	
	function draw(){
		$data = array();
		StaticP::autoEdit($this, $data, 'draw');
    }

    function on_submit(){
		$url = Url::getParam('url');
        $lang = Url::getParam('lang', '');
        $pid = Url::getParam('pid', '');
		$type = Url::getParam('type', '');
        if($pid != ''){
            $url = $pid.'-'.$lang;
        }
		
		if(FunctionLib::isUrlString($url)){
			$action = Url::getParamAdmin('action');

			if($action == 'edit'){
				$pageListKey = isset(CGlobal::$configs[$this->key]) ? unserialize(CGlobal::$configs[$this->key]) : array();
				if($url != $this->page['url'] && isset($pageListKey[$url])){
					$this->setFormError('page_url', 'Url đã tồn tại');
				}
			}else if(isset(CGlobal::$configs[$url])){
				$this->setFormError('page_url', 'Url đã tồn tại');
			}

			if($this->errNum == 0){
				//update mang key de check
				$pageListKey = isset(CGlobal::$configs[$this->key]) ? unserialize(CGlobal::$configs[$this->key]) : array();
				
				//xoa trang cu
				if($this->page['url'] != $url){
					DB::delete(T_CONFIGS, "conf_key='".$this->page['url']."'");
					unset($pageListKey[$this->page['url']]);
				}
				
				//valid lai cho dep
				foreach($pageListKey as $k => $v){
					if(!isset(CGlobal::$configs[$k])){
						unset($pageListKey[$k]);
					}
				}

				//gan key moi
				$pageListKey[$url] = 1;
				ConfigSite::setConfigToDB($this->key, serialize($pageListKey));
				
				//chen noi dung trang moi
				$page = array(
					'title'	  => Url::getParam('title'),
					'content' => Url::getParam('content'),
					'url'	  => $url,
                    'pid'     => $pid,
                    'lang'    => $lang,
					'type'	  => $type
				);
                ConfigSite::setConfigToDB($url, serialize($page));

				//delete cached
				StaticP::clearCacheStaticPage($url);

				Url::redirect('admin', array('cmd' => 'trang-tinh'));
			}
		}else{
			$this->setFormError('page_url', 'Url không hợp lệ');
		}
	}
}