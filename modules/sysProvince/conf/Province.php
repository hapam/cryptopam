<?php
class Province{
	static function provinceAutoList(&$form, $data = array()){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET'
		));
		
		//add group search
		$form->layout->addGroup('main', array('title' => 'Thông tin'));
		
		//add item to search
		$form->layout->addItem('title', array(
			'type'	=> 'text',
			'title' => 'Tên tỉnh thành'
		), 'main');
		
		//add item to view
		$form->layout->addItemView('btn-del-check', array(
			'per'	=>	$form->perm['del'],
			'type'	=>	'del',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('id', array(
			'title' => 'ID',
			'order' => true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('position', array(
			'title' => 'Sort',
			'order' => true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('title', array(
			'title' => 'Tên tỉnh thành',
			'order' => true
		));
		$form->layout->addItemView('information', array(
			'title' => 'Thông tin chính'
		));
		$form->layout->addItemView('address', array(
			'title' => 'Địa chỉ'
		));
		$form->layout->addItemView('contact', array(
			'title' => 'Liên lạc'
		));
		$form->layout->addItemView('status_icon', array(
			'title' => 'Active',
			'type'  => 'icon',
			'only' => true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-edit', array(
			'title' =>	'Sửa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['edit'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-del', array(
			'title' =>	'Xóa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['del'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		
		return $form->layout->genFormAuto($form, $data);
	}
	
	static function provinceAutoEdit(&$form, &$data = array(), $action = ''){
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST',
			'onsubmit'	=>	'shop.admin.province.onSubmit'
		));

		//add group
		$form->layout->addGroup('main', array('title' => 'Thuộc tính'));
		$form->layout->addGroup('contact', array('title' => 'Liên hệ'));
		$form->layout->addGroup('cskh', array('title' => 'Chăm sóc khách hàng'));

		//add form item by Group main
		$form->layout->addItem('id', array(
			'type'	=> 'hidden',
			'value' => $form->id,
			'save'  => false
		), 'main');
		$form->layout->addItem('title', array(
			'type'	=> 'text',
			'title' => 'Tên tỉnh thành',
			'value' => Url::getParam('title', $form->province['title']),
			'required' => true
		), 'main');
		$status = array('' => 'Chọn trạng thái', '0' => 'Bình thường', '1' => 'Kích hoạt vùng miền', '-1' => 'Xóa');
		$form->layout->addItem('status', array(
			'number'=> true,
			'type'	=> 'select',
			'title' => 'Trạng thái',
			'options' => FunctionLib::getOption($status, Url::getParamInt('status',$form->province['status']))
		), 'main');
		$is_city = array('0' => 'Cấp tỉnh', '1' => 'Cấp Thành phố');
		$form->layout->addItem('is_city', array(
			'number'=> true,
			'type'	=> 'select',
			'title' => 'Phân loại',
			'options' => FunctionLib::getOption($is_city, Url::getParamInt('is_city',$form->province['is_city']))
		), 'main');
		$form->layout->addItem('position', array(
			'number'=> true,
			'type'	=> 'text',
			'title' => 'Sắp xếp',
			'value' => Url::getParamInt('position',$form->province['position']),
			'ext' => array(
				'onkeypress' => 'return shop.numberOnly(this, event)',
				'maxlength'  => 5
			),
			'caption' => 'Theo thứ tự từ nhỏ đến lớn'
		), 'main');
		
		//add form item by Group contact
		$form->layout->addItem('hotline', array(
			'type'	=> 'text',
			'title' => 'Hotline',
			'value' => Url::getParam('hotline',$form->province['hotline'])
		), 'contact');
		$form->layout->addItem('fax', array(
			'type'	=> 'text',
			'title' => 'Fax',
			'value' => Url::getParam('fax',$form->province['fax'])
		), 'contact');
		$form->layout->addItem('email', array(
			'type'	=> 'text',
			'title' => 'Email',
			'value' => Url::getParam('email',$form->province['email'])
		), 'contact');
		$form->layout->addItem('address', array(
			'type'	=> 'text',
			'title' => 'Địa chỉ',
			'value' => Url::getParam('address',$form->province['address'])
		), 'contact');
		$form->layout->addItem('name_facebook', array(
			'type'	=> 'text',
			'title' => 'FB Fanpage',
			'value' => Url::getParam('name_facebook', $form->province['name_facebook'])
		), 'contact');
		
		if(isset($data['cskh_html'])){
			$form->layout->addItem('skype-yahoo', array(
				'type'	=> 'html',
				'html' => $data['cskh_html']
			), 'cskh');
		}
		
		if($action == 'draw'){
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
}