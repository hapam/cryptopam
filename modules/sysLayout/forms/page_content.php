<?php
class PageContentForm extends Form{
	var $page=array(),$layout_text='',$layout_text_mobile='',$layout=null,$undefined_regions=false,$regions=array();
	function __construct(){
		$id = Url::getParamInt('id');
		if($id > 0){
			$this->page = DB::select(T_PAGE, 'id='.$id);
			if($this->page){
				$this->page = $this->page[$id];
				$this->page['themes'] = $this->page['themes'] != '' ? $this->page['themes'] : CGlobal::$configs['themes'];
				$this->page['themes_mobile'] = $this->page['themes_mobile'] != '' ? $this->page['themes_mobile'] : CGlobal::$configs['themes_mobile'];
			}
		}
		
		if(!$this->page){
			Url::redirect_current();
		}
	}
	
	function draw(){
		global $display;
		
		$re = DB::query('SELECT b.id, b.module_id, b.page_id, b.region, b.position,b.mobile,m.name,m.themes,m.themes_mobile
						FROM '.T_BLOCK.' b INNER JOIN '.T_MODULE.' m ON m.id=module_id
						WHERE page_id='.$_REQUEST['id'].' ORDER BY position');
		$this->all_blocks = array();
		if($re){
			while ($block = mysql_fetch_assoc($re)){
				$this->all_blocks[$block['id']]=$block;
			}
		}

		//layout website
		$this->layout_text = $this->get_txt_layout($this->page['layout'], false, $this->page['themes']);
		$this->get_regions(false, $this->page['themes']);
		$text = $this->layout_text.($this->undefined_regions?'<p><h1>Các module ngoài Layout</h1>[[|undefined_regions|]]</p>':'');
		$result = '';
		while(($pos = strpos($text,'[[|'))!==false){
			if($pos2 = strpos($text,'|',$pos+3)){
				$var = substr($text, $pos+3,  $pos2-$pos-3);
				if(isset($this->regions[$var])){
					$item = $this->regions[$var];
				}
				if($item){
					$result .= substr($text, 0,  $pos).$item;
					$text = substr($text, $pos2+3,  strlen($text)-$pos2-3);
				}
				else{
					$result .= substr($text, 0,  $pos+3);
					$text = substr($text, $pos+3,  strlen($text)-$pos-3);
				}
			}
			else{
				$result .= substr($text, 0,  $pos+3);
				$text = substr($text, $pos+3,  strlen($text)-$pos-3);
			}
		}
		$regions = $result.$text;

		$regions_mobile = '';
		$opt_layout_mobile = '';
		if(CGlobal::$configs['themes_mobile'] != 'no_mobile'){
			$opt_layout_mobile = FunctionLib::getOption($this->get_all_layouts(true, $this->page['themes_mobile']),Url::getParam('status_mobile',$this->page['layout_mobile']));
			//layout mobile
			$this->layout_text_mobile = $this->get_txt_layout($this->page['layout_mobile'], true, $this->page['themes_mobile']);
			$this->get_regions(true, $this->page['themes_mobile']);
			$text = $this->layout_text_mobile.($this->undefined_regions?'<p><h1>Các module ngoài Layout</h1>[[|undefined_regions|]]</p>':'');
			$result = '';
			while(($pos = strpos($text,'[[|'))!==false){
				if($pos2 = strpos($text,'|',$pos+3)){
					$var = substr($text, $pos+3,  $pos2-$pos-3);
					if(isset($this->regions[$var])){
						$item = $this->regions[$var];
					}
					if($item){
						$result .= substr($text, 0,  $pos).$item;
						$text = substr($text, $pos2+3,  strlen($text)-$pos2-3);
					}
					else{
						$result .= substr($text, 0,  $pos+3);
						$text = substr($text, $pos+3,  strlen($text)-$pos-3);
					}
				}
				else{
					$result .= substr($text, 0,  $pos+3);
					$text = substr($text, $pos+3,  strlen($text)-$pos-3);
				}
			}
			$regions_mobile = $result.$text;
		}
		
		$display->add('name',$this->page['name']);
		$display->add('id',$this->page['id']);
		$display->add('regions',$regions);
		$display->add('regions_mobile',$regions_mobile);
		$display->add('option_layout',FunctionLib::getOption($this->get_all_layouts(false, $this->page['themes']),Url::getParam('status',$this->page['layout'])));
		$display->add('option_layout_mobile', $opt_layout_mobile);
		$display->add('page_list', $this->get_all_pages($this->page['id']));
		$display->add('page_content',$this->page);
		$display->output('page_content');
	}
	
	function get_regions($mobile = false, $themes = ''){
		$this->regions = array();
		if(preg_match_all('/\[\[\|([^\|]+)\|\]\]/i', $mobile ? $this->layout_text_mobile : $this->layout_text, $region_matchs,PREG_SET_ORDER)){		
			$region_s = '';
			foreach($region_matchs as $region){
				$region_s .= ($region_s?',':'').'"'.$region[1].'"';
				$this->regions[$region[1]] = '';
			}
			$modules = array();
			if($region_s){
				$res = DB::query("SELECT * FROM ".T_BLOCK." WHERE page_id = {$this->page['id']} AND region IN ($region_s) AND mobile = ".($mobile ? 1 : 0)." ORDER BY position");
				while($row = @mysql_fetch_assoc($res)){
					$moduleInfo = DB::fetch("SELECT * FROM ".T_MODULE." WHERE id = ".$row['module_id']);

					//neu dang load layout mobile
					if($mobile && $moduleInfo['themes_mobile'] != ''){
						//neu giao dien mobile dc kich hoat ma module ko phai cua giao dien do thi next
						if(CGlobal::$configs['themes_mobile'] != 'no_mobile' && CGlobal::$configs['themes_mobile'] != $moduleInfo['themes_mobile']){
							continue;
						}
					}elseif(CGlobal::$configs['themes'] != '' && $moduleInfo['themes'] != '' && CGlobal::$configs['themes'] != $moduleInfo['themes']){
						continue;
					}
					$row['name'] = $moduleInfo['name'];
					$row['themes'] = $moduleInfo['themes'];
					$row['themes_mobile'] = $moduleInfo['themes_mobile'];
					$modules[$row['region']][$row['id']] = $row;
					unset($this->all_blocks[$row['id']]);
				}
			}
			foreach ($this->regions as $region=>$val){
				if(!isset($modules[$region])){
					$modules[$region] = array();
				}
				$this->regions[$region] = $this->draw_list($region, $modules[$region], $mobile);
			}
			unset($modules);
		}
		//kiem tra & ve them vung undefined
		foreach($this->all_blocks as $k => $v){
			if($mobile){
				if($v['mobile'] == 0){
					unset($this->all_blocks[$k]);
				}
			}else{
				if($v['mobile'] == 1){
					unset($this->all_blocks[$k]);
				}
			}
		}
		$this->undefined_regions = !empty($this->all_blocks);
		if($this->undefined_regions){
			$this->regions['undefined_regions'] = $this->draw_list('undefined_regions', $this->all_blocks);
		}
	}
	
	function get_all_pages($default_id = 0){
		$link = Url::buildAdminURL("edit_page");
		$pages = array();
		$res = DB::query('SELECT id, name, themes FROM '.T_PAGE.' ORDER BY name');
		while ($row = @mysql_fetch_assoc($res)){
			$pages[$row['id']] = $row['name'];
		}
		return FunctionLib::getOption($pages, $default_id);
	}
	
	function get_all_layouts($mobile = false, $themes = ''){
		$path = ROOT_PATH;
		if(!isset(CGlobal::$corePages[$this->page['name']])){
			$path = DIR_THEMES;
			if($mobile && CGlobal::$configs['themes_mobile'] != 'no_mobile'){
				$path .= 'mobile/'.CGlobal::$configs['themes_mobile'].'/';
			}else{
				$path .= 'website/'.($themes != '' ? $themes : CGlobal::$configs['themes']).'/';
			}
		}
		
		$layouts = array(''=>'-- Chọn layout --');
		$dir = opendir($path.'layouts');
		while($file = readdir($dir)){
			if(($file != '.') && ($file != '..') && is_file($path.'layouts/'.$file)){
				$layouts['layouts/'.$file] = basename($file,'.'.FileHandler::getExtension($file,'html'));
			}
		}
		closedir($dir);
		return $layouts;
	}
	
	function get_txt_layout($file_name = '', $mobile = false, $themes = ''){
		$layouts = '';
		if($file_name != ''){
			$path = ROOT_PATH;
			if(!isset(CGlobal::$corePages[$this->page['name']])){
				$path = DIR_THEMES;
				if($mobile && CGlobal::$configs['themes_mobile'] != 'no_mobile'){
					$path .= 'mobile/'.CGlobal::$configs['themes_mobile'].'/';
				}else{
					$path .= 'website/'.($themes != '' ? $themes : CGlobal::$configs['themes']).'/';
				}
			}
			if(file_exists($path.$file_name)){
				$layouts = file_get_contents($path.$file_name);
			}
		}
		return $layouts;
	}
	
	function draw_list($region = '', $modules = array(), $mobile = false){
		global $display;
		$mobile = $mobile ? 1 : 0;
		if(!empty($modules)){
			if($region != 'undefined_regions'){
				$i = 0;
				$last = false;
				foreach ($modules as $key=>$item){
					if($i){
						if($i>1){
							$last['move_up']  = '<a href="'.Url::build_current(array('cmd'=>'move','id'=>$this->page['id'],'block_id'=>$last['id'],'move'=>'up','mobile'=>$mobile)).'"><img src="css/images/MoveUp.png" alt="Move up"></a>';
							$last['move_top'] = '<a href="'.Url::buildAdminURL('edit_page',array('id'=>$this->page['id'],'block_id'=>$last['id'],'cmd'=>'move_top','mobile'=>$mobile)).'">MoveTop</a>';
						}
						$last['move_down']   = '<a href="'.Url::buildAdminURL('edit_page',array('cmd'=>'move','id'=>$this->page['id'],'block_id'=>$last['id'],'move'=>'down','mobile'=>$mobile)).'"><img src="css/images/MoveDown.png" alt="Move down"></a>';
						$last['move_bottom'] = '<a href="'.Url::buildAdminURL('edit_page',array('id'=>$this->page['id'],'block_id'=>$last['id'],'cmd'=>'move_bottom','mobile'=>$mobile)).'">MoveBottom</a>';
					}
					$i++;
					
					$last = &$modules[$key];
					$last['move_up']	=	'';
					$last['move_down']	=	'';
				}
				if($i>1){
					$modules[$key]['move_up']  = '<a href="'.Url::buildAdminURL('edit_page',array('cmd'=>'move','id'=>$this->page['id'],'block_id'=>$item['id'],'move'=>'up','mobile'=>$mobile)).'"><img src="css/images/MoveUp.png" alt="Move up"></a>';
					$modules[$key]['move_top'] = '<a href="'.Url::buildAdminURL('edit_page',array('id'=>$this->page['id'],'block_id'=>$item['id'],'cmd'=>'move_top','mobile'=>$mobile)).'">MoveTop</a>';
				}
			}else{
				$ids = implode('|', array_keys($modules));
				$delFromUndefined = Url::buildAdminURL('edit_page', array('cmd' => 'delete_all_block', 'ids' => $ids, 'id' => $this->page['id']));
				$display->add('delFromUndefined', $delFromUndefined);
			}
		}
		$display->add('hover', FunctionLib::mouse_hover('#CCCCCC',true));
		$display->add('id', $this->page['id']);
		$display->add('name', $region);
		$display->add('items', $modules);
		$display->add('mobile', $mobile);
		$display->add('moduleLink', Url::build('module'));
		return $display->output('list_block',true);
	}
}
