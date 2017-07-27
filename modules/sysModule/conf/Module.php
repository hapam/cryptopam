<?php
class Module{
	static	$block_id = 0,
			$name = '',
			$themes = '',
			$themes_mobile = '',
			$dir = '',
			$shown = array(),
			$init = array();

	var $data = false,
		$forms = array();

	function Module($row){
		$this->data = $row;

		Module::$block_id 	= $this->data['id'];
		Module::$name 		= $this->data['module']['name'];
		Module::$dir[Module::$name] = DIR_MODULE;

		if($this->data['module']['themes'] != ''){
			Module::$dir[Module::$name] = DIR_THEMES.'website/'.$this->data['module']['themes'].'/modules/';
		}elseif($this->data['module']['themes_mobile'] != ''){
			Module::$dir[Module::$name] = DIR_THEMES.'mobile/'.$this->data['module']['themes_mobile'].'/modules/';
		}
	}

	static function permission(){}

	function add_form($sub_form = false){
		if(!empty($sub_form)){
			$sub_form->name = get_class ($sub_form);
			$this->forms[] = $sub_form;
		}
	}

	function submit(){
		Module::$block_id 	= $this->data['id'];
		Module::$name 		= $this->data['module']['name'];
		$this->on_submit();

		Module::$block_id 	= 0;
		Module::$name 		= '';
	}

	function on_submit(){
		if($this->forms){
			foreach ($this->forms as $sub_form){
				$sub_form->on_submit();
			}
		}
	}

	function draw($region = ''){
		if($this->forms){
			foreach($this->forms as $sub_form){
				if(($sub_form->region == '' || $sub_form->region == $region) && !isset(Module::$shown[$sub_form->name])){
					if(DEBUG) {
						$start_block = microtime(true);
					}
					$sub_form->on_draw($region);
					if(DEBUG) {
						CGlobal::$arrModuleDebug[Module::$name]["form"][$sub_form->name] = (microtime(true) - $start_block);
					}
					Module::$shown[$sub_form->name] = 1;
				}
			}
		}
	}

	function on_draw($region = ''){
		Module::$block_id 	= $this->data['id'];
		Module::$name 		= $this->data['module']['name'];

		RootPanel::drawModuleOnEditmode(false, Module::$name, Module::$block_id);
		$this->draw($region);
		RootPanel::drawModuleOnEditmode(true);

		Module::$block_id 	= 0;
		Module::$name 		= '';
	}
}
