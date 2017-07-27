<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_sysModuleAuto {
	function playme() {
		$code = Url::getParam('code');
		switch( $code ) {
			case 'load-table':
				$this->loadTable();
				break;
			default: $this->home();
		}
	}
	function loadTable(){
		$table = Url::getParam('table');
		$edit_mode = Url::getParamInt('edit_mode', 0);
		$search_form = Url::getParamInt('search_form', 0);
		$html = '';
		if($table != ''){
			$tables = array();
			$res = DB::query('SHOW COLUMNS FROM '.$table);
			while($r = @mysql_fetch_assoc($res)){
				$tmp = explode('(', $r['Type']);
				$r['t'] = $tmp[0];
				$r['l'] = isset($tmp[1]) ? substr($tmp[1], 0, -1) : 0;
				$tables[$r['Field']] = array(
					'name' => $r['Field'],
					'type'  => $r['t'],
					'length'  => $r['l']
				);
			}
			$form = new Form('basicForm');
			foreach($tables as $k => $t){
				$html2 = '';
				if($search_form == 1){
					$checked = array('id', 'title', 'status', 'created');
					$html2 = $form->layout->genItemHtml($form->layout->parseItem('filter_'.$k, array(
						'label' => 'Tìm kiếm',
						'type'  => 'checkbox',
						'style' => 'onoff',
						'label_pos' => 'left',
						'checked' => in_array($k, $checked)
					)));
				}
				$checked = array('id', 'title', 'created', 'sort', 'weight', 'image');
				$html2.= $form->layout->genItemHtml($form->layout->parseItem('show_'.$k, array(
					'label' => 'Hiển thị',
					'type'  => 'checkbox',
					'style' => 'onoff',
					'label_pos' => 'left',
					'checked' => in_array($k, $checked)
				)));
				if($edit_mode == 1){
					$checked = array('title', 'body', 'description', 'note', 'sort', 'weight', 'image');
					$html2.= $form->layout->genItemHtml($form->layout->parseItem('edit_'.$k, array(
						'label' => 'Cho nhập',
						'type'  => 'checkbox',
						'style' => 'onoff',
						'label_pos' => 'left',
						'checked' => in_array($k, $checked)
					)));
					$checked = array('title');
					$html2.= $form->layout->genItemHtml($form->layout->parseItem('require_'.$k, array(
						'label' => 'Bắt buộc nhập',
						'type'  => 'checkbox',
						'style' => 'onoff',
						'label_pos' => 'left',
						'checked' => in_array($k, $checked)
					)));
				}
				$formInputs = array(
					'text' => 'Text',
					'number' => 'Text Number',
					'select' => 'Select box',
					'checkbox' => 'Checkbox',
					'checkbox-onoff' => 'Checkbox On-Off',
					'checkbox-group' => 'Checkbox Group',
					'radio' => 'Radio',
					'radio-group' => 'Radio Group',
					'textarea' => 'Text Area',
					'textarea-fck' => 'Text Area + Editor',
					'file' => 'File',
					'password' => 'Password',
					'time' => 'Text Time'
				);
				$checked = array('id' => 'number', 'title' => 'text', 'created' => 'time', 'status' => 'select', 'image' => 'file', 'sort' => 'number', 'weight' => 'number', 'body' => 'textarea', 'note' => 'textarea', 'description' => 'textarea');
				$html2.= $form->layout->genItemHtml($form->layout->parseItem('type_'.$k, array(
					'type'	=> 'select',
					'title' => 'Loại form input',
					'options'=> FunctionLib::getOption($formInputs, (isset($checked[$k]) ? $checked[$k] : 'text'))
				)));
				$checked = array('id' => 'ID', 'title' => 'Tiêu đề', 'created' => 'Ngày tạo', 'image' => 'Hình ảnh', 'status' => 'Trạng thái', 'sort' => 'Sắp xếp', 'weight' => 'Sắp xếp', 'body' => 'Nội dung', 'description' => 'Mô tả', 'note' => 'Ghi chú');
				$html2.= $form->layout->genItemHtml($form->layout->parseItem('title_'.$k, array(
					'type'	=> 'text',
					'title' => 'Tiêu đề',
					'value' => isset($checked[$k]) ? $checked[$k] : ''
				)));
				$checked = array(
					'status' => '1 => Bình thường
-1 => Xóa' 
				);
				$html2.= $form->layout->genItemHtml($form->layout->parseItem('option_'.$k, array(
					'type'	=> 'textarea',
					'title' => 'Danh sách lựa chọn',
					'value' => isset($checked[$k]) ? $checked[$k] : '',
					'ext' => array(
						'rows' => 5
					),
					'caption' => 'Cách viết: <b>0 => Ẩn</b><br />Mỗi giá trị trên 1 dòng'
				)));
				$html2.= $form->layout->genItemHtml($form->layout->parseItem('button_'.$k, array(
					'type'	=> 'button',
					'title' => 'Không dùng',
					'style' => 0,
					'color' => 'red',
					'icon'  => 'clear',
					'size'  => 2,
					'ext'   => array(
						'onclick' => "shop.moduleAuto.delInput('p{$k}_{$t['type']}', '{$k}')"
					)
				)));
			
				$html.= $form->layout->genPanelAuto(array(
					'id' => $k.'_'.$t['type'],
					'title' => $k.' ('.$t['type'].' - '.$t['length'].')',
					'color_head' => 'green',
					'html'  => $html2
				));
			}
		}
		FunctionLib::JsonSuccess('done', array('html' => $html), true);
	}
	function home() {
		die("Nothing to do...");
	}
}