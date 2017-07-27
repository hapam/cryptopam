<?php
class Form{
	var $name = false,
		$region = '',
		$error_messages = false,
		$succes_messages = false,
		$errNum = 0,
		$layout;
	
	function __construct($name = ''){
		$this->name = ($name != '') ? $name : get_called_class();
		$this->layout = new FormLayout($this->name, $this);
	}
	
	function beginForm($upload=false, $method='post', $target=false, $action=false, $return=false,$ext=""){
		if(!$this->layout){
			$this->layout = new FormLayout($this->name);
		}
		$html = $this->layout->beginForm($upload, $method, $target, $action, $return,$ext);
		if($return){
			return  $html;
		}
		echo $html;
	}

	function endForm($return = false){
		if(!$this->layout){
			$this->layout = new FormLayout($this->name);
		}
		$html = $this->layout->endForm($return);
		if($return){
			return  $html;
		}
		echo $html;
	}

	function link_css($file_name, $web = false){
		$src = $web ? $file_name : WEB_ROOT.$file_name.'?ver='.CGlobal::$css_ver;
		if(strpos(Layout::$extraHeaderCSS,'<link rel="stylesheet" href="'.$src.'" type="text/css">')===false){
			Layout::$extraHeaderCSS .= '<link rel="stylesheet" href="'.$src.'" type="text/css">'."\n";
		}
	}
	
	function link_css_me($filecss = '', $path = __FILE__){
		$path = str_replace(array(ROOT_PATH, 'forms/'), array('', ''), strtr(dirname($path) ."/",array('\\'=>'/')));
		$this->link_css($path.'css/'.$filecss);
	}

	function link_js($file_name, $web = false){
		$src = $web ? $file_name : (WEB_ROOT.$file_name.'?ver='.CGlobal::$js_ver);
        if(strpos(Layout::$extraFooter,'<script type="text/javascript" src="'.$src.'"></script>')===false){
            Layout::$extraFooter .= '<script type="text/javascript" src="'.$src.'"></script>'."\n";
        }
	}
	
	function link_js_me($filejs = '', $path = __FILE__){
		$path = str_replace(array(ROOT_PATH, 'forms/'), array('', ''), strtr(dirname($path) ."/",array('\\'=>'/')));
		$this->link_js($path.'js/'.$filejs);
	}

	function link_header($text){
		Layout::$extraHeader .= $text."\n";
	}
	
	function link_footer($text){
		Layout::$extraFooter .= $text."\n";
	}

	function setFormError($name, $msg){
		@$this->error_messages[$this->name][$name] = $msg;
		$this->errNum++;
	}

	function showFormErrorMessages($return=false,$error_title='Thông báo lỗi'){
		if(!$this->layout){
			$this->layout = new FormLayout($this->name);
		}
		$txt = $this->layout->showFormMsg(0, $error_title, $this->error_messages);
		if($return)return $txt;
		else echo $txt;
	}
	
	function setFormSucces($name, $msg){
		@$this->succes_messages[$this->name][$name] = $msg;
		$this->errNum++;
	}
	
	function showFormSuccesMessages($return=false,$error_title='THÔNG BÁO'){
		if(!$this->layout){
			$this->layout = new FormLayout($this->name);
		}
		$txt = $this->layout->showFormMsg(1, $error_title, $this->succes_messages);
		if($return)return $txt;
		else echo $txt;
	}
	
	function draw(){}

	function on_draw(){$this->draw();}
	
	function on_submit(){}
	
	function auto_submit(&$data = array()){
		foreach($this->layout->items as $k => $item){
			if($item['save'] && !in_array($item['type'], array('html', 'button', 'file'))){
				$getVal = $item['number'] ? Url::getParamInt($k, 0) : Url::getParam($k, '');
				if($item['required'] && $getVal == ''){
					$this->setFormError($k, $item['title']." là trường bắt buộc");
				}else{
					$data[$k] = $getVal;
				}
			}
		}
		return $this->errNum <= 0;
	}
}