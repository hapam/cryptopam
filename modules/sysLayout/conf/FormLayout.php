<?php
class FormLayout{
	var $name = '',
		$tplPath = 'sysLayout/themes/default',
		$config,
		$group,
		$items,
		$form;
	
	function __construct($name = '', $form = false){
		$this->name = $name;
		if($form){
			$this->form = $form;
		}
		$this->addGroup('main', array(
			'title' => 'Thông tin chính'
		));
	}
	
	//style=add|edit|list|free - method=POST|GET - onsubmit='' - onback='' - del=true|false - upload:true|false - path:tpl direc
	function init($config = array()){
		$config['id'] = $this->name;
		if(!isset($config['del'])){
			$config['del'] = true;
		}
		if(!isset($config['form'])){
			$config['form'] = true;
		}
		if(isset($config['upload'])){
			global $uploadFile;
			$uploadFile = $config['upload'];
		}
		if(!isset($config['method'])){
			$config['method'] = "POST";
		}
		if(isset($config['path']) && !empty($config['path'])){
			$this->tplPath = $config['path'];
		}
		$this->config = $config;
	}
	
	function reset(){
		$this->config = array();
		$this->items = array();
		$this->group = array();
		$this->addGroup('main', array(
			'title' => 'Thông tin chính'
		));
	}
	
	//id, title, toggle:true|false, hide:true|false, size:array('lg' => 12, 'md' => 12, 'sm' => 12, 'xs' => 12), type: search|edit|
	function addGroup($key = '', $ele = array(), $pKey = ''){
		$ele['id'] = 'formGroup-'.$key;
		$ele['items'] = array();
		$ele['sub'] = array();
		if(!isset($ele['per'])){
			$ele['per'] = true;
		}
		if(!isset($ele['type'])){
			$ele['type'] = 'search';
		}
		if(!isset($ele['header'])){
			$ele['header'] = true;
		}
		if(!isset($ele['size'])){
			$ele['size'] = array('lg' => 12, 'md' => 12, 'sm' => 12, 'xs' => 12);
		}else{
			if(!isset($ele['size']['lg'])) $ele['size']['lg'] = 12;
			if(!isset($ele['size']['md'])) $ele['size']['md'] = 12;
			if(!isset($ele['size']['sm'])) $ele['size']['sm'] = 12;
			if(!isset($ele['size']['xs'])) $ele['size']['xs'] = 12;
		}
		if($pKey != ''){
			$this->group[$pKey]['sub'][$key] = $ele;
		}else{
			$this->group[$key] = $ele;
		}
	}
	
	function getGroup($key = ''){
		return isset($this->group[$key]) ? $this->group[$key] : array();
	}
	
	function removeGroup($key = ''){
		if(isset($this->group[$key])){
			unset($this->group[$key]);
			return true;
		}
		return false;
	}
	
	function addItem($key = '', $ele = array(), $group = 'main', $groupParent = '', $view = false){
		$this->items[($view?'view-':'').$key] = $this->parseItem($key, $ele, $view);
		if($group){
			if($groupParent != ''){
				if(isset($this->group[$groupParent])){
					if(isset($this->group[$groupParent]['sub'][$group])){
						$this->group[$groupParent]['sub'][$group]['items'][$key] = $key;
						return true;
					}
				}
			}elseif(isset($this->group[$group])){
				$this->group[$group]['items'][$key] = $key;
				return true;
			}
		}
		return false;
	}
	
	function getItem($key = ''){
		return isset($this->items[$key]) ? $this->items[$key] : array();
	}
	
	function parseItem($key = '', $ele = array(), $view = false){
		$ele['id'] = $key;
		if(!isset($ele['per'])){
			$ele['per'] = true;
		}
		if(!isset($ele['line'])){
			$ele['line'] = true;
		}
		if(!$view){
			if(isset($ele['caption']) && $ele['caption'] != ''){
				$ele['caption'] = $ele['caption'];
			}
			if(!isset($ele['save'])){
				$ele['save'] = true;
			}
			if(!isset($ele['number'])){
				$ele['number'] = false;
			}
			if(!isset($ele['required'])){
				$ele['required'] = false;
			}
			if(!isset($ele['value'])){
				if($ele['type'] == 'checkbox' || $ele['type'] == 'radio'){
					$ele['value'] = 1;
				}else{
					$ele['value'] = $ele['number'] ? Url::getParamInt($key, 0) : Url::getParam($key, '');
				}
			}elseif($ele['type'] == 'checkbox' || $ele['type'] == 'radio'){
				$ele['value'] = $ele['value'] < 1 ? 1 : $ele['value'];
			}
			if($ele['type'] == 'button'){
				if(!isset($ele['size'])){
					$ele['size'] = 0;
				}
				$ele['size_tit'] = $this->addSizeButton($ele['size']);
				$ele['color'] = $this->addColorButton(isset($ele['color']) ? $ele['color'] : '');
			}
		}else{
			if(!isset($ele['hide'])){
				$ele['hide'] = false;
			}
			$ele['view-item'] = true;
			$ele['save'] = false;
		}
		return $ele;
	}
	
	function addItemView($key = '', $ele = array()){
		$this->addItem($key, $ele, '', '', true);
	}
	
	function getItemView($key = '', $list = false){
		if($list){
			$arr = array();
			if(!empty($this->items)){
				foreach($this->items as $k => $v){
					if(isset($v['view-item']) && $v['view-item']){
						$arr[$v['id']] = $v;
					}
				}
			}
			return $arr;
		}
		return isset($this->items['view-'.$key]) ? $this->items['view-'.$key] : array();
	}

	function beginForm($upload=false, $method='post', $target=false, $action=false, $return=false,$ext=""){
		$fixNoRewrite = '';
		if(!REWRITE_ON && $method=='get'){
			$q = $_SERVER['QUERY_STRING'];
			$q = str_replace(array('q=','no_search'),array('','do_search'),$q);
			$fixNoRewrite = '<input type="hidden" name="q" value="'.$q.'" />';
		}
		$html = '<form '.(($this->name)?'name="'.$this->name.'" id="'.$this->name.'" ':'').' method="'.$method.'" '.($upload?' enctype="multipart/form-data" ':'').($target?' target="'.$target.'" ':'').($action?' action="'.$action.'" ':'').$ext.' >';
		$html.= $fixNoRewrite.'<input type="hidden" name="'.TOKEN_KEY_NAME.'" value="'.CGlobal::$tokenData.'">';
		if(Module::$block_id){
			$html.= '<input type="hidden" name="form_block_id" value="'.Module::$block_id.'">';
		}
		return  $html;
	}

	function endForm($return = false){
		return   '</form>';
	}

	function showFormMsg($type = 0, $title = '', $data = array()){
		$output = '';
		if(isset($data[$this->name])){
			global $display;
			$tpl = $type == 0 ? 'msg_error' : 'msg_success';

			$display->add('msg_title', $title);
			$display->add('msg_data', $data[$this->name]);
			$output = $display->output($tpl, true, $this->tplPath);
		}
		return $output;
	}
	
	function genItemHtml($data = array()){
		global $display, $uploadFile, $include;

		$out = '';
		if(!empty($data)){
			if($data['type'] == 'file'){
				$uploadFile = true;
			}
			if(isset($data['time']) && $data['time'] && !isset($include['time'])){
				$include['time'] = true;
				$this->form->link_css("plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css");
				$this->form->link_js("plugins/momentjs/moment.js");
				$this->form->link_js("plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js");
			}
			if(isset($data['editor']) && $data['editor'] && !isset($include['editor'])){
				$include['editor'] = true;
				$this->form->link_js("plugins/ckeditor/ckeditor.js");
				if(isset($data['image']) && $data['image']){
					Gallery::addMultiUploadCore($this->form, 'multiupload.js');
				}
			}
			$display->add('formItem', $data);
			$out = $display->output('input_form', true, $this->tplPath);
		}
		return $out;
	}
	
	function genGroupItemHtml($group = array()){
		$out = '';
		if(!empty($group)){
			global $display;
	
			if(!empty($group['items'])){
				foreach($group['items'] as $k => $v){
					if(isset($this->items[$v])){
						$group['items'][$k] = $this->genItemHtml($this->items[$v]);
					}else{
						unset($group['items'][$k]);
					}
				}
			}
			$display->add('formGroup', $group);
			$out = $display->output('panel_edit_form', true, $this->tplPath);
		}
		return $out;
	}
	
	/*
		$dataList: html_search | html_search_label | html_search_button | html_view | html_view_header | html_view_buttons | html_view_label | html_view_table | html_view_table_content
		
	*/
	function genFormAuto($form, $dataList = array('items' => array(), 'pagging' => array('start_page' => 0, 'total_item' => 0, 'total_page' => 1, 'pager' => '', 'html' => '')), $return = false){
		global $display;
		global $uploadFile;
		$conf = $this->config;
		$conf['name'] = $this->name;

		$msg = $form->showFormErrorMessages(1);
		if(empty($msg)){
			$msg = $form->showFormSuccesMessages(1);
		}

		if($conf['style'] == 'add' || $conf['style'] == 'edit'){
			if(!isset($conf['onback'])){
				$conf['onback'] = "history.go(-1)";
			}
			foreach($this->group as $k => $group){
				if(!empty($group['sub'])){
					foreach($group['sub'] as $kk => $group_sub){
						$group['sub'][$kk]['html'] = $this->genGroupItemHtml($group_sub);
					}
				}
				$this->group[$k]['html'] = $this->genGroupItemHtml($group);
			}
			
			$display->add('msg', $msg);
			$display->add('formGroupItems', $this->group);
			$display->add('formConf', $conf);
			$display->add('formData', $dataList);
			
			if($return){
				$out = $form->beginForm($uploadFile, $conf['method'], false, false, $return);
				$out.= $display->output('page_edit', $return, $this->tplPath);
				$out.= $form->endForm($return);
				return $out;
			}else{
				$form->beginForm($uploadFile, $conf['method']);
				$display->output('page_edit', false, $this->tplPath);
				$form->endForm();
			}
			return true;
		}elseif($conf['style'] == 'list' || $conf['style'] == 'grid'){
			if($conf['style'] == 'list'){
				foreach($this->group as $k => $group){
					foreach($group['items'] as $idx => $item){
						if(isset($this->items[$item])){
							$this->group[$k]['items'][$idx] = $this->genItemHtml($this->items[$item]);
						}
					}
				}
				
				$display->add('msg', $msg);
				$display->add('searchWidth', round(100/count($this->group)));
				$display->add('formSearch', $this->group);
			}
			$display->add('formView', $this->getItemView('', true));
			$display->add('formConf', $conf);
			$display->add('data', $dataList);
			if(isset($form->perm)){
				$display->add('per', $form->perm);
			}
			if(isset($form->link)){
				$display->add('link', $form->link);
			}

			if($return){
				$out = '';
				if($conf['style'] == 'list'){
					if($conf['form']){
						$out = $form->beginForm($uploadFile, $conf['method'], false, false, $return);
					}
					$out.= $display->output('page_list', $return, $this->tplPath);
					if($conf['form']){
						$out.= $form->endForm($return);
					}
				}else{
					$out = $display->output('table_list_view', $return, $this->tplPath);
				}
				return $out;
			}else{
				if($conf['style'] == 'list'){
					if($conf['form']){
						$form->beginForm($uploadFile, $conf['method']);
					}
					$display->output('page_list', $return, $this->tplPath);
					if($conf['form']){
						$form->endForm();
					}
				}else{
					$display->output('table_list_view', $return, $this->tplPath);
				}
			}
			return true;
		}elseif($conf['style'] == 'html'){
			$display->add('msg', $msg);
			$display->add('formConf', $conf);
			$display->add('data', $dataList);
			if($return){
				$out = '';
				if($conf['form']){
					$out = $form->beginForm($uploadFile, $conf['method'], false, false, $return);
				}
				$out.= $display->output('page_html', $return, $this->tplPath);
				if($conf['form']){
					$out.= $form->endForm($return);
				}
				return $out;
			}
			if($conf['form']){
				$form->beginForm($uploadFile, $conf['method']);
			}
			$display->output('page_html', $return, $this->tplPath);
			if($conf['form']){
				$form->endForm();
			}
		}
		return false;
	}
	
	function genLabelAuto($label = array('title' => 'DEMO LABEL', 'des' => 'Demo description')){
		global $display;
		$display->add('label', $label);
		return $display->output('label', true, $this->tplPath);
	}
	
	function genHeaderAuto($label = array('title' => 'DEMO LABEL', 'des' => 'Demo description', 'class' => '', 'ext' => '')){
		global $display;
		$display->add('label', $label);
		return $display->output('header', true, $this->tplPath);
	}
	
	function genPanelAuto($p = array('id' => 'demo-panel', 'size' => array(), 'title' => 'DEMO Panel', 'html' => 'Demo description', 'header' => true, 'color_head' => '', 'color_body' => '', 'toggle' => false, 'hide' => false, 'per' => true), $return = true){
		global $display;
		if(!isset($p['per'])){
			$p['per'] = true;
		}
		if(!isset($p['title'])){
			$p['title'] = 'DEMO';
		}
		if(!isset($p['id'])){
			$p['id'] = substr(md5($p['title']),0,8);
		}
		if(!isset($p['header'])){
			$p['header'] = true;
		}
		if(!isset($p['size'])){
			$p['size'] = array('lg' => 12, 'md' => 12, 'sm' => 12, 'xs' => 12);
		}else{
			if(!isset($p['size']['lg'])) $p['size']['lg'] = 12;
			if(!isset($p['size']['md'])) $p['size']['md'] = 12;
			if(!isset($p['size']['sm'])) $p['size']['sm'] = 12;
			if(!isset($p['size']['xs'])) $p['size']['xs'] = 12;
		}
		$display->add('panel', $p);
		if($return){
			return $display->output('panel_html', $return, $this->tplPath);
		}
		$display->output('panel_html', $return, $this->tplPath);
	}
	
	//items: active=true|false - id:string - title:string - icon:string - html:string
	function genTabAuto($items = array(), $return = true){
		global $display;
		$p = array('items' => $items, 'per' => true);
		if(!empty($p['items'])){
			$noActive = true;
			foreach($p['items'] as $k => $v){
				if(isset($v['active']) && $v['active']){
					$noActive = false;
					break;
				}
			}
			if($noActive){
				$p['items'][0]['active'] = true;
			}
		}
		$display->add('tab', $p);
		if($return){
			return $display->output('tabs', $return, $this->tplPath);
		}
		$display->output('tabs', $return, $this->tplPath);
	}
	
//	type:0-button submit|1:button|2:link
//  style: 0-not circle|1:circle
//	size: 0:default|1:lg|2:sm|3:xs
	function genButtonAuto($button = array('title' => '', 'icon' => 'search', 'style' => 1, 'size' => 0, 'color' => 'purple', 'type' => 0, 'per' => true, 'ext' => array())){
		global $display;
		if(!isset($button['per'])){
			$button['per'] = true;
		}
		if($button['per']){
			$button['color'] = $this->addColorButton(isset($button['color']) ? $button['color'] : '');
			if(!isset($button['size'])){
				$button['size'] = 0;
			}
			if(!isset($button['type'])){
				$button['type'] = 0;
			}
			if(!isset($button['style'])){
				$button['style'] = 1;
			}
			if(!isset($button['title']) && !isset($button['icon'])){
				$button['title'] = 'Button';
			}
			$button['size_tit'] = $this->addSizeButton($button['size']);
			$display->add('button', $button);
			return $display->output('button', true, $this->tplPath);
		}
		return '';
	}
	
	function addColorButton($color){
		$arrColor = array('red', 'pink', 'purple', 'deep-purple', 'indigo', 'blue', 'light-blue', 'cyan', 'teal', 'green', 'light-green', 'lime', 'yellow', 'amber', 'orange', 'deep-orange', 'brown', 'grey', 'blue-grey', 'black');
		$arrColor2= array('default', 'primary', 'danger', 'success', 'warning', 'info');
		if(in_array($color, $arrColor)){
			return 'bg-'.$color;
		}elseif(in_array($color, $arrColor2)){
			return 'btn-'.$color;
		}
		return 'btn-default';
	}
	
	function addSizeButton($size){
		$arrSize  = array(1 => 'lg', 2 => 'sm', 3 => 'xs');
		if(isset($arrSize[$size])){
			return $arrSize[$size];
		}
		return '';
	}
}