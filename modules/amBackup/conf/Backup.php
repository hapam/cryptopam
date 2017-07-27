<?php
class Backup{
	static function getLink($name = '', $time = 0) {
        return ImageUrl::getImageServerUrl() . self::getDir($time) . $name;
    }
	static function getLinkDir($name = '', $time = 0, $ser = true) {
        return ($ser ? ROOT_PATH . IMAGE_PATH_STATIC : '') . self::getDir($time) . $name;
    }
	static function getDir($time = 0) {
        return BACKUP_FOLDER . FileHandler::createdDirByTime($time);
    }
	
	
	static function autoList(&$form, $data = array()){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET',
			'del'		=>	false
		));
		
		//add group search
		$form->layout->addGroup('main', array('title' => 'Khời tạo từ ngày'));
		$form->layout->addGroup('main2', array('title' => 'Đến ngày'));
		
		//add item to search
		$form->layout->addItem('created_time', array(
			'type'	=> 'text',
			'holder'=> 'Ext: 30-07-2016',
			'time'  => true,
			'value' => Url::getParam('created_time','')
		), 'main');
		
		$form->layout->addItem('created_time_to', array(
			'type'	=> 'text',
			'holder'=> 'Ext: 30-07-2016',
			'time'  => true,
			'value' => Url::getParam('created_time_to','')
		), 'main2');
		
		//add item to view
		$form->layout->addItemView('index', array(
			'title' => 'STT',
			'type' => 'index',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('name', array(
			'title' => 'Tên file',
			'order' => true
		));
		$form->layout->addItemView('created', array(
			'title' => 'Ngày tạo',
			'head' => array(
				'width' => '100'
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('download', array(
			'title' =>	'Tải về',
			'type'  =>	'icon',
			'icon'	=>	'cloud_download',
			'per' => $form->perm['edit'],
			'head' => array(
				'width' => '70'
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('restore', array(
			'title' =>	'Khôi phục',
			'type'  =>	'icon',
			'icon'	=>	'restore',
			'per' => $form->perm['edit'],
			'head' => array(
				'width' => '100'
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('delete', array(
			'title' =>	'Xóa',
			'type'  =>	'icon',
			'icon'	=>	'delete',
			'per' => $form->perm['del'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$data['html_view_buttons'] = $form->layout->genButtonAuto(array(
			'title' => '&nbsp;Tạo backup',
			'icon'  => 'backup',
			'style' => 0,
			'color' => 'green',
			'type'  => 1,
			'size'  => 0,
			'ext'   => array(
				'onclick' => 'javascript:shop.backup.add()'
			),
			'per' => $form->perm['add']
		));
		$form->layout->genFormAuto($form, $data);
	}
}