<?php

class StaticP{
	static $time = 31536000;
	static $dir  = 'configs/';
	static function getPageContent($def_url = '', $no_cache = false){
		$cached = array();
		$pageListKey = isset(CGlobal::$configs['static_page_key']) ? unserialize(CGlobal::$configs['static_page_key']) : array();
		if(Language::$haveToTranslate){
			$def_url .= '-'.Language::$activeLang;
		}
		if(isset($pageListKey[$def_url])){
			if(!$no_cache){
				$cached = CacheLib::get($def_url, self::$time, self::$dir);
			}
			if(empty($cached)){
				$cached = DB::fetch("SELECT * FROM ".T_CONFIGS." WHERE conf_key = '$def_url'");
				if(!empty($cached) && !$no_cache){
					CacheLib::set($def_url, $cached, self::$time, self::$dir);
				}
			}
		}
		return isset($cached['conf_val']) ? unserialize($cached['conf_val']) : array();
	}
	
	static function getPageList(){
		$data = array();
		$pageListKey = isset(CGlobal::$configs['static_page_key']) ? unserialize(CGlobal::$configs['static_page_key']) : array();
		if(!empty($pageListKey)){
			$allKeyPages = implode("','", array_keys($pageListKey));
			$res = DB::query("SELECT * FROM ".T_CONFIGS." WHERE conf_key IN ('$allKeyPages')");
			while($v = @mysql_fetch_assoc($res)){
				CGlobal::$configs[$v['conf_key']] = $v['conf_val'];
				$data[$v['conf_key']] = $v['conf_val'];
			}
		}
		return $data;
	}
	
	static function clearCacheStaticPage($def_url){
		//xoa cache trang
		CacheLib::delete($def_url, self::$dir);

		//xoa cache config
		ConfigSite::clearCacheConfig();
	}
	
	static function autoList(&$form, $data = array()){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'POST',
			'del'		=>  false
		));
		$data['html_search'] = '&nbsp;';
		$data['html_view_label'] = $form->layout->genLabelAuto(array('title' => "TRANG TĨNH", 'des' => "Tạo ra các landing page cho website"));

		//add view table
		$form->layout->addItemView('index', array(
			'title' => 'STT',
			'type' => 'index',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('title', array(
			'title' => 'Tiêu đề'
		));
		$form->layout->addItemView('t_url', array(
			'title' => 'URL'
		));
		if($form->perm['edit']){
			$langList = Language::$listLangOptions;
			$langDef = Language::$defaultLang;
			foreach($langList as $k => $i){
				if($k != $langDef){
					$form->layout->addItemView($k, array(
						'title' => $k,
						'head' => array(
							'width' => 50
						),
						'ext' => array(
							'align' => 'center'
						)
					));
				}
			}
		}
		$form->layout->addItemView('btn-edit', array(
			'title' =>	'Sửa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['edit'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-del', array(
			'title' =>	'Xóa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['del'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));		
		return $form->layout->genFormAuto($form, $data);
	}
	
	static function autoEdit(&$form, &$data = array(), $action = ''){
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST'
		));
		$form->layout->addGroup('main', array('title' => 'Thông tin cơ bản'));
		
		//add form
		$form->layout->addItem('title', array(
			'type'	=> 'text',
			'title' => 'Tiêu đề trang',
			'required' => true,
			'value' => Url::getParam('title', $form->page['title'])
		), 'main');
		$typeArr = array('page' => 'Xuất trang URL', 'html' => 'Chỉ lưu HTML');
		$form->layout->addItem('type', array(
			'type'	=> 'select',
			'title' => 'Cách thức lưu trữ',
			'options' => FunctionLib::getOption($typeArr, Url::getParam('type', $form->page['type']))
		), 'main');
		$form->layout->addItem('url', array(
			'type'	=> 'text',
			'title' => 'URL',
			'required' => true,
			'value' => Url::getParam('url', $form->page['url']),
			'caption' => "Chỉ chấp nhận kí tự (a-z, A-Z), số (0-9), dấu '_', '-', bắt đầu phải là kí tự"
		), 'main');
		$form->layout->addItem('content', array(
			'type'	=> 'textarea',
			'title' => 'Nội dung',
			'editor'=> true,
			'image' => true,
			'width' => 700,
			'height'=> 300,
			'value' => Url::getParam('content', $form->page['content'])
		), 'main');
		
		
		if($action == 'draw'){
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
}