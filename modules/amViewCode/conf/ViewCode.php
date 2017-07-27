<?php

class ViewCode{

	static function runSQLAuto(&$form, $data){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET'
		));
		
		//add group search
		$form->layout->addGroup('main', array('title' => 'Tables'));
		$form->layout->addGroup('gr-fields', array('title' => 'Fields & Conditions'));
		$form->layout->addGroup('gr-limit', array('title' => 'Limit'));
		$form->layout->addGroup('gr-order', array('title' => 'Order'));
		
		//add item to search
		$form->layout->addItem('mode', array(
			'type'	=> 'hidden',
			'value' => Url::getParam('mode', 'runSQL')
		), 'main');
		$table = Url::getParam('table','');
		$all_table = DB::fetch_all("SHOW TABLES FROM ".DB_MASTER_NAME);
		$option_table = '<option value="">Table</option>';
		foreach ($all_table as $k_tb => $v_tb) {
			$option_table .= '<option value="'.$v_tb['Tables_in_'.DB_MASTER_NAME].'"';
			if($k_tb==='' && $table==='')
			{
				$option_table .=  ' selected';
			}
			else
			if( $k_tb!=='' && $v_tb['Tables_in_'.DB_MASTER_NAME]==$table )
			{
				$option_table .=  ' selected';
			}
			$option_table .= '>'.$v_tb['Tables_in_'.DB_MASTER_NAME].'</option>';
		}
		$form->layout->addItem('table', array(
			'type'	=> 'select',
			'title' => 'Tables',
			'options' => $option_table
		), 'main');
		
		$form->layout->addItem('field', array(
			'type'	=> 'text',
			'title'	=> 'Field - Phân cách bởi dấu phẩy',
			'value' => Url::getParam('field','*')
		), 'gr-fields');
		
		$form->layout->addItem('conditions', array(
			'type'	=> 'text',
			'title'	=> 'Conditions',
			'value' => Url::getParam('conditions','')
		), 'gr-fields');
		
		$form->layout->addItem('limit', array(
			'number'=> true,
			'type'	=> 'text',
			'title'	=> 'Limit - RecPerPage',
			'value' => Url::getParamInt('limit', 100),
			'ext' => array(
				'onkeypress' => 'return shop.numberOnly(this, event)',
				'maxlength'  => 5
			),
		), 'gr-limit');
		
		$form->layout->addItem('field_orderby', array(
			'type'	=> 'text',
			'title'	=> 'Field Order',
			'value' => Url::getParam('field_orderby','')
		), 'gr-order');
		
		$arr_orderby = array('DESC'=>'Giảm dần','ASC'=>'Tăng dần');
		$form->layout->addItem('orderby', array(
			'type'	=> 'select',
			'title' => 'Order By',
			'options' => FunctionLib::getOption($arr_orderby, Url::getParam('orderby','ASC'))
		), 'gr-order');
		
		$data['html_search_label'] = $form->layout->genLabelAuto(array(
			'title' => 'TRUY VẤN CSDL'
		));
		$data['html_search_button'] = $form->layout->genButtonAuto(array(
			'title' => 'Chạy lệnh SQL',
			'style' => 0,
			'color' => 'red',
			'type'  => 0,
			'size'  => 0,
			'icon'  => 'broken_image'
		));
		$data['html_view_label'] = $form->layout->genLabelAuto(array(
			'title' => ($table != '') ? 'KẾT QUẢ TRUY VẤN BẢNG <em>'.$table.'</em>' : 'KẾT QUẢ'
		));
		$data['html_view_buttons'] = $form->layout->genButtonAuto(array(
			'title' => 'Xem Code Online',
			'style' => 0,
			'color' => 'purple',
			'type'  => 2,
			'size'  => 0,
			'icon'  => 'visibility',
			'ext'   => array(
				'href' => Url::build('admin', array('cmd' => 'view-code'))
			),
			'per' => User::is_root()
		));
		$form->layout->genFormAuto($form, $data);
	}
	
	static function viewCodeAuto(&$form, $data, $err = ''){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET'
		));

		$data['html_view_label'] = $form->layout->genLabelAuto(array(
			'title' => $data['bread'],
			'des' => '<small><b>SERVER: '.$_SERVER['SERVER_ADDR'].'</b></small>'
		));
		
		$data['html_view_buttons'] = $form->layout->genButtonAuto(array(
			'title' => 'Chạy SQL Online',
			'style' => 0,
			'color' => 'red',
			'type'  => 2,
			'size'  => 0,
			'icon'  => 'broken_image',
			'ext'   => array(
				'href' => Url::build('admin', array('cmd' => 'view-code'), '?mode=runSQL')
			),
			'per' => User::is_root()
		));

		if($err != ''){
			$data = array(
				'html_search' => '&nbsp;',
				'html_view_label' => $data['html_view_label'],
				'html_view_buttons' => $data['html_view_buttons'],
				'html_view_table' => $err
			);
		}
		$form->layout->genFormAuto($form, $data);
	}
}

