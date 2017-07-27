<?php
class HelpForm extends Form {
	function __construct() {
		parent::__construct();
	}

	function draw() {
		global $display;

		$this->layout->init(array('style' => 'html'));
		$html = $this->layout->genPanelAuto(array(
			'id' => 'basic-required',
			'title' => 'Hàm khởi tạo',
			'color_head' => 'green',
			'color_body' => 'blue-grey',
			'html' => $display->output('req_construct', true)
		));
		
		$html .= $this->layout->genPanelAuto(array(
			'id' => 'list-required',
			'title' => 'Form List',
			'color_head' => 'green',
			'color_body' => 'blue-grey',
			'html' => $display->output('req_list', true)
		));
		
		$html .= $this->layout->genPanelAuto(array(
			'id' => 'edit-required',
			'title' => 'Form Edit',
			'color_head' => 'green',
			'color_body' => 'blue-grey',
			'html' => $display->output('req_edit', true)
		));
		
		$html = '<div class="row">'.$html.'</div>';
		$this->layout->genFormAuto($this, array('html' => $html));
	}
}