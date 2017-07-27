<?php 
	require_once(ROOT_PATH.'includes/smarty/Smarty.class.php');
	
	class TplLoad extends Smarty {
		function TplLoad(){
			$this->Smarty();
			$this->template_dir = ROOT_PATH."modules/";
			$this->compile_dir 	= DIR_CACHE."templates_c/";
			$this->cache_dir 	= DIR_CACHE."smarty/";
			$this->config_dir 	= DIR_CACHE."configs/";
			$this->cache_ext 	= ".tpl";
			$this->caching 		= false;
	
			//tao thu muc neu khong ton tai
			FileHandler::CheckDir($this->compile_dir);
			FileHandler::CheckDir($this->cache_dir);
			FileHandler::CheckDir($this->config_dir);
	
			//$this->load_filter('output','trimwhitespace');
			
			//$this->debugging = true;
		}
		function add($key = '' , $value = ''){
			$this->assign($key,$value);
		}	
		
		//Chi dung cho module, neu ngoai module dung display()...
		function output($template = '', $return = false, $direct_dir_path = false) {  
			if($direct_dir_path){
				$template_dir = $this->template_dir . $direct_dir_path.'/';
			}else{
				$template_dir = !empty(Module::$name) ? (Module::$dir[Module::$name].Module::$name.'/tpl/') : ($this->template_dir.$template.'/');
			}
			FileHandler::CheckDir($template_dir);
			
			$template = !empty($template) ? ($template_dir.$template.$this->cache_ext) : "";
			if($return){
				return $this->fetch($template);
			}
			$this->display($template);
		}	
	}
