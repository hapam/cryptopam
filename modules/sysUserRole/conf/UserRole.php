<?php
class UserRole{
	static function autoList(&$form, $data = array()){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'POST'
		));
		$data['html_search'] = '&nbsp;';
		$data['html_view_label'] = $form->layout->genLabelAuto(array('title' => "NHÓM QUYỀN", 'des' => "Hết sức thận trọng khi thao tác"));

		//add view table
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
		$form->layout->addItemView('title', array(
			'title' => 'Tên nhóm quyền'
		));
		$form->layout->addItemView('rank', array(
			'title' => 'Rank'
		));
		$form->layout->addItemView('permit', array(
			'title' => 'Danh sách quyền'
		));
		$form->layout->addItemView('created', array(
			'title' => 'Ngày tạo',
			'head' => array(
				'width' => 100
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('perm', array(
			'title' => 'Quyền',
			'type'  =>	'icon',
			'per'   => $form->perm['per'],
			'icon' => 'verified_user',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('cache', array(
			'title' => 'Cache',
			'type'  =>	'icon',
			'per'   => $form->perm['edit'],
			'icon' => 'cached',
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
	
	static function autoEdit(&$form, &$data = array(), $action = ''){
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST'
		));
		
		//add form group
		$form->layout->addGroup('main', array('title' => 'Thông tin cơ bản'));
		
		if($data['access']){
			//add control
			$form->layout->addItem('id', array(
				'type'	=> 'hidden',
				'number'=> true,
				'save' => false,
				'value' => Url::getParamInt('id', $form->id)
			), 'main');
			$form->layout->addItem('title', array(
				'type'	=> 'text',
				'title' => 'Tên nhóm quyền',
				'required' => true,
				'value' => Url::getParam('title', $form->item['title'])
			), 'main');
			$form->layout->addItem('rank', array(
				'type'	=> 'text',
				'title' => 'Thứ hạng',
				'required' => true,
				'number' => true,
				'value' => Url::getParamInt('rank', $form->item['rank']),
				'ext' => array(
					'onkeypress' => 'return shop.numberOnly(this, event)',
					'maxlength'  => 11
				)
			), 'main');
		}else{
			$form->layout->addItem('error', array(
				'type'	=> 'html',
				'html' => 'Bạn không có quyền thay đổi nhóm quyền cao hơn mình'
			), 'main');
		}
		
		if($action == 'draw'){
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
	
	static function autoPermission(&$form, &$data = array(), $action = ''){
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST'
		));
		$form->layout->addGroup('main', array('title' => 'GÁN QUYỀN CHO NHÓM: <em>'.$data['items']['title'].'</em>'));
		
		if($data['access']){
			$form->layout->addItem('error', array(
				'type'	=> 'html',
				'html' => '<div><b>ID:</b> '.$data['items']['id'].'</div><div><b>Rank:</b> '.$data['items']['rank'].'</div>'
			), 'main');
			foreach($data['permission'] as $title => $perms){
				$form->layout->addGroup($title, array('title' => $title, 'toggle' => true));
				foreach($perms as $k => $p){
					$form->layout->addGroup($k, array('title' => $k, 'toggle' => true), $title);
					$max = count($p);
					$counter = 0;
					foreach($p as $role => $t){
						$counter++;
						$form->layout->addItem($role, array(
							'type'	=> 'checkbox',
							'style' => 'onoff',
							'label_pos' => 'left',
							'label' => "&nbsp;&nbsp; $t",
							'checked' => self::have_permit($data['items']['permit'], $role),
							'line' => $counter < $max
						), $k, $title);
					}
				}
			}
		}else{
			$form->layout->addItem('error', array(
				'type'	=> 'html',
				'html' => 'Bạn không có quyền thay đổi nhóm quyền cao hơn mình'
			), 'main');
		}
		
		if($action == 'draw'){
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
	
	static function have_permit($group_perm, $permit){
		$permit = str_replace(' ','_',$permit);
		return stripos($group_perm.',', $permit.',') !== false;
	}
}