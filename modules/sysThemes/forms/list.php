<?php
class ListThemesForm extends Form{
	var $themes, $allInfo, $keywords='themes', $current_theme = 'default', $current_mobile_theme = 'no_mobile', $name_preview = 'preview.jpg', $default_preview = '';
	function __construct(){
		parent::__construct();
		if(Url::getParamAdmin('cmd', '') == 'delete_cache'){
			$this->cleanCached();
		}
		$this->default_preview = WEB_ROOT.'style/images/no_preview.jpg';
		$this->current_theme = CGlobal::$configs[$this->keywords];
		if(isset(CGlobal::$configs[$this->keywords.'_mobile'])){
			$this->current_mobile_theme = CGlobal::$configs[$this->keywords.'_mobile'];
		}
	}
	
	function draw(){
		if(isset($_SESSION['success_alert']) && $_SESSION['success_alert'] != ''){
			$this->setFormSucces('', $_SESSION['success_alert']);
			$_SESSION['success_alert'] = '';
		}
		$themes = $this->listThemesInDir();
		$webOptions = array();
		foreach($themes as $name => $data){
			//$webOptions[$name] = '<img src="'.$data['preview'].'" width="100" style="border:1px solid #fff;padding:2px;background:#fff;cursor:pointer" /> '.$name;
			$webOptions[$name] = $name;
		}
		$themes = $this->listThemesInDir(false, true);
		$mobileOptions = array();
		foreach($themes as $name => $data){
			$mobileOptions[$name] = $name == 'no_mobile' ? 'Không sử dụng theme mobile' : $name;
		}
		
		$this->layout->init(array(
			'style' => 'edit',
			'method'=> 'POST'
		));
		$this->layout->addGroup('main', array('title' => 'Danh sách themes website'));
		$this->layout->addGroup('mobile', array('title' => 'Danh sách themes mobile'));
		
		$this->layout->addItem('themes', array(
			'type' => 'radio-group',
			'value' => $this->current_theme,
			'options' => $webOptions,
			'style' => 1,
			'line' => false
		), 'main');
		
		$this->layout->addItem('themes_mobile', array(
			'type' => 'radio-group',
			'value' => $this->current_mobile_theme,
			'options' => $mobileOptions,
			'style' => 1,
			'line' => false
		), 'mobile');
		
		$this->layout->genFormAuto($this, array(
			'html_button_submit' => $this->layout->genButtonAuto(array(
				'title'=> 'Cài đặt Theme',
				'icon' => 'extension',
				'style'=> 0,
				'size' => 1,
				'color' => 'primary'
			)),
			'html_button_cancel' => $this->layout->genButtonAuto(array(
				'icon' => 'cached',
				'style'=> 0,
				'size' => 1,
				'type' => 2,
				'title' => 'Xóa cache',
				'color' => 'pink',
				'icon' => 'delete_forever',
				'ext' => array(
					'href' => Url::buildAdminURL('themes',array('cmd'=>'delete_cache')),
					'style'  => 'margin-left:20px'
				)
			))
		));
	}
	
	function on_submit(){
		$name = Url::getParam('themes', $this->current_theme);
		$name_mobile = Url::getParam('themes_mobile', $this->current_mobile_theme);

		//insert DB for theme website
        ConfigSite::setConfigToDB($this->keywords, $name);

		//insert DB for theme mobile
        ConfigSite::setConfigToDB($this->keywords.'_mobile', $name_mobile);

		//xóa cache
		$this->cleanCached("Themes <b><em>$names</em></b> đã cài đặt thành công");
	}

	function listThemesInDir($no_fetch = false, $mobile = false){
        $more = array();
		if($mobile){
			$more['no_mobile'] = $no_fetch ? 'no_mobile' : array('name' => 'no_mobile');
            //if(CGlobal::$configs['themes_mobile'] == 'no_mobile'){
            //    return $more;
            //}
		}
		$dir_theme = DIR_THEMES.($mobile ? 'mobile' : 'website').'/';
		$web_theme = WEB_THEMES.($mobile ? 'mobile' : 'website').'/';
        
        $theme_dirs = @scandir($dir_theme);
		if($theme_dirs){
			unset($theme_dirs[0]);
			unset($theme_dirs[1]);
			if(isset($theme_dirs[2]) && $theme_dirs[2] == '.DS_Store'){
				unset($theme_dirs[2]);
			}
	
			foreach($theme_dirs as $name){
				$dir = $dir_theme.$name;
				if($no_fetch){
					$more[$name] = $name;
				}else{
					$more[$name] = array(
						'dir' => $dir,
						'name' => $name,
						'last_access' => fileatime ($dir),
						'last_modified' => filemtime ($dir),
						'last_changed' => filectime($dir),
						'preview' => file_exists($dir.'/'.$this->name_preview) ? ($web_theme.$name.'/'.$this->name_preview) : $this->default_preview
					);
				}
			}
		}
		return $more;
	}
	
	function cleanCached($msg = ''){
		//xoa cache config
		ConfigSite::clearCacheConfig();

		//cap nhat lai cache trang sau khi xoa module
        CacheLib::delete('arr_page');
		Layout::update_all_page();
        
        //tra ve thong bao thanh cong
		$_SESSION['success_alert'] = $msg ? $msg : "Cache đã được cập nhật";
		Url::redirect('themes'); 
	}
}
