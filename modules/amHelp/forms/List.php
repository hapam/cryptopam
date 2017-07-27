<?php
class ListForm extends Form {
	function __construct() {
		parent::__construct();
	}

	function draw() {
		global $display;

		$this->layout->init(array('style' => 'html'));
		$display->add('link_req', Url::build('admin', array('cmd' => 'help', 'action' => 'req')));
		$display->add('link_input', Url::build('admin', array('cmd' => 'help', 'action' => 'input-form')));
		$html = $this->layout->genPanelAuto(array(
			'id' => 'basic-required',
			'title' => 'Yều cầu bắt buộc',
			'color_head' => 'green',
			'color_body' => 'blue-grey',
			'html' => $display->output('list_req', true)
		));
		
		$html .= $this->layout->genPanelAuto(array(
			'id' => 'list-required',
			'title' => 'Cấu hình & Hiển thị',
			'color_head' => 'green',
			'color_body' => 'blue-grey',
			'html' => $display->output('list_view', true)
		));
		
		$html .= $this->layout->genPanelAuto(array(
			'id' => 'search-required',
			'title' => 'Tìm kiếm',
			'color_head' => 'green',
			'color_body' => 'blue-grey',
			'html' => $display->output('list_search', true)
		));
		
		$html .= $this->layout->genPanelAuto(array(
			'id' => 'view-required',
			'title' => 'Kết quả',
			'color_head' => 'green',
			'color_body' => 'blue-grey',
			'html' => $display->output('list_result', true)
		));
		
		$html = '<div class="row">'.$html.'</div>';
		$this->layout->genFormAuto($this, array('html' => $html));
	}
}