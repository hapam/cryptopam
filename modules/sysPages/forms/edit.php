<?php
class EditPageAdminForm extends Form{
	private $cmd;
	public $page, $default_theme = 'sys', $default_theme_mobile = 'no_mobile';
	function __construct(){
		parent::__construct();
		$this->cmd = Url::getParamAdmin('cmd');
		if($this->cmd == 'edit' || $this->cmd == 'copy'){			
			$id = Url::getParamInt('id',0);
			if($id > 0){
				if($this->page = DB::select(T_PAGE,'id='.$id)){
					$this->page = $this->page[$id];
					$this->page['is_core'] = isset(CGlobal::$corePages[$this->page['name']]);
					$this->page['no_delete'] = isset(CGlobal::$noDeletePages[$this->page['name']]);
				}
			}
			if(!$this->page){
				Url::redirect_current();
			}
		}
		
		$this->link_js_me('admin_page.js', __FILE__);
	}
	
	function draw(){	
		global $display;
		
		if($this->cmd == 'edit'){
			if($this->page['themes'] != ''){
				$this->default_theme = $this->page['themes'];
			}
			if($this->page['themes_mobile'] != ''){
				$this->default_theme_mobile = $this->page['themes_mobile'];
			}
		}
		$data = array(
			'theme_web' => $this->themeOpt(),
			'theme_mobile' => $this->themeOpt(true),
			'mode' => $this->cmd,
			'layoutOptions' => $this->get_all_layouts(false, $this->page['themes'], $this->page['admin'] == 1),
			'layoutMobileOptions' => CGlobal::$configs['themes_mobile'] != 'no_mobile' ? $this->get_all_layouts(true, CGlobal::$configs['themes_mobile']) : '',
			
		);
		
		PAGE::autoEdit($this, $data, 'draw');
	}
	
	function themeOpt($mobile = false){
		require_once(ROOT_PATH.'/modules/sysThemes/forms/list.php');
		$theme = new ListThemesForm();
		$option = $theme->listThemesInDir(true, $mobile);
		if($mobile){
			if(CGlobal::$configs['themes_mobile'] != 'no_mobile'){
				unset($option[CGlobal::$configs['themes_mobile']]);
			}
			$option = array('no_mobile' => 'no_mobile') + $option;
		}else{
			if(CGlobal::$configs['themes'] != 'sys'){
				unset($option[CGlobal::$configs['themes']]);
			}
			$option = array('sys' => 'sys') + $option;
		}
		return $option;
	}
	
	function get_all_layouts($mobile = false, $themes = '', $admin = false){
		$path = DIR_THEMES;
        $layouts = array(''=>'-- Chọn layout --');
		if($admin){
			$path = ROOT_PATH;
		}else{
			if($mobile){
				if(CGlobal::$configs['themes_mobile'] != 'no_mobile'){
					if($this->page['themes_mobile'] != ''){
						$path .= 'mobile/'.$this->page['themes_mobile'].'/';
					}else{
						$path .= 'mobile/'.CGlobal::$configs['themes_mobile'].'/';
					}
				}else{
					return $layouts;
				}
			}else{
				$path .= 'website/'.($themes != '' ? $themes : CGlobal::$configs['themes']).'/';
			}
		}
		
		$dir = opendir($path.'layouts');
		while($file = readdir($dir)){
			if(($file != '.') && ($file != '..') && is_file($path.'layouts/'.$file)){
				$layouts['layouts/'.$file] = basename($file,'.'.FileHandler::getExtension($file,'html'));
			}
		}
		closedir($dir);
		return $layouts;
	}
	
	function on_submit(){
		$new_row = array(
			'theme_web' => array(),
			'theme_mobile' => array(),
			'mode' => $this->cmd,
			'layoutOptions' => array(),
			'layoutMobileOptions' => CGlobal::$configs['themes_mobile'] != 'no_mobile' ? array('no_mobile' => 'no_mobile') : '',
			
		);
		if(PAGE::autoEdit($this, $new_row, 'submit')){
			unset($new_row['theme_web']);
			unset($new_row['theme_mobile']);
			unset($new_row['mode']);
			unset($new_row['layoutOptions']);
			unset($new_row['layoutMobileOptions']);
			if($new_row['themes'] == $this->default_theme){
				$new_row['themes'] = '';
			}
			if($new_row['themes_mobile'] == $this->default_theme_mobile){
				$new_row['themes_mobile'] = '';
			}
			if($this->cmd == 'copy'){
				if($new_row['name'] == $this->page['name'] || DB::select(T_PAGE,'name="'.$new_row['name'].'"')){
					$this->setFormError('name','URL "'.$new_row['name'].'" đã tồn tại, hãy chọn URL khác!');
					return ;
				}

				$id = DB::insert(T_PAGE, $new_row);
				if($id > 0){
					$re = DB::query('SELECT id, module_id, page_id, region, position FROM '.T_BLOCK.' WHERE page_id='.$this->page['id']);
					if($re){
						while($row = mysql_fetch_assoc($re)){
							unset($row['id']);
							$row['page_id'] = $id;
							DB::insert(T_BLOCK,$row);
						}
					}
				}
			}									
			elseif($this->cmd == 'edit'){
				DB::update(T_PAGE, $new_row,'id='.$this->page['id']);
				Layout::update_page($this->page['id']);
				CacheLib::delete('arr_page');
			}
			else{
				$id = DB::insert(T_PAGE, $new_row);
				CacheLib::delete('arr_page');
			}
			Url::redirect('page');
		}
	}
}
