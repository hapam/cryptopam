<?php
class PAGE{
	static function autoList(&$form, $data = array()){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET'
		));
		$data['html_search_header'] = '&nbsp;';
		if(!$data['search']){
			$data['html_extra_foot'] = self::autoListCore($form, $data);
		}
		$buttons = $form->layout->genButtonAuto(array(
            'title' => 'Xóa Cache',
            'style' => 0,
            'color' => 'pink',
            'type'  => 2,
            'size'  => 1,
			'icon' => 'delete_forever',
            'ext' => array(
                'href' => $form->link['cache']
            )
        ));
		$buttons .= '&nbsp;&nbsp;&nbsp;';
		$buttons .= $form->layout->genButtonAuto(array(
            'style' => 1,
			'icon'  => 'search',
            'color' => 'purple',
            'size'  => 1
	    ));
		$data['html_search_button'] = $buttons;

		//add group search
		$form->layout->addGroup('main', array('title' => 'Tìm theo URL'));
		
		//add item to search
		$form->layout->addItem('name', array(
			'type'	=> 'text',
			'title' => 'URL'
		), 'main');
		
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
		$form->layout->addItemView('url', array(
			'title' => 'URL',
			'order' => true
		));
		$form->layout->addItemView('title', array(
			'title' => 'Tiêu đề'
		));
		$form->layout->addItemView('themes', array(
			'title' => 'Themes'
		));
		$form->layout->addItemView('layout', array(
			'title' => 'Bố cục'
		));
		$form->layout->addItemView('key_icon', array(
			'title' => 'Key',
			'type'  =>	'icon',
			'only'	=>	true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('des_icon', array(
			'title' => 'Des',
			'type'  =>	'icon',
			'only'	=>	true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('view', array(
			'title' => 'Xem',
			'type'  =>	'icon',
			'per'   => $form->perm['edit'],
			'icon' => 'search',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('copy', array(
			'title' => 'Copy',
			'type'  =>	'icon',
			'per'   => $form->perm['edit'],
			'icon' => 'content_copy',
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
	
	static function autoListCore($form, $data){
		$newform = new Form('pageCore');
        $newform->layout->init(array(
			'style'	=>	'list',
			'form'	=>	false
		));
		$newform->layout->addItemView('id', array(
			'title' => 'ID',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
        $newform->layout->addItemView('url', array(
			'title' => 'URL'
		));
		$newform->layout->addItemView('title', array(
			'title' => 'Tiêu đề'
		));
		$newform->layout->addItemView('description', array(
			'title' => 'Mô tả'
		));
		$newform->layout->addItemView('layout', array(
			'title' => 'Bố cục'
		));
		$newform->link = $form->link;
		$newform->layout->addItemView('btn-edit', array(
			'title' =>	'Sửa',
			'type'  =>	'icon',
			'per'	=>	User::is_root(),
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
        return $newform->layout->genFormAuto($newform, array(
            'items' => $data['cores'],
            'html_view_label' => $newform->layout->genLabelAuto(array('title' => 'CORE PAGE', 'des' => 'Trang của hệ thống, chỉ xem không thao tác')),
            'html_search' => '&nbsp;'
        ), true);
	}
	
	static function autoEdit(&$form, &$data = array(), $action = ''){
		$admin_page = Url::getParamInt('admin', $form->page['admin']);
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST'
		));
		
		//add group
		$form->layout->addGroup('main', array('title' => 'Thông tin cơ bản'));
		$form->layout->addGroup('seo', array('title' => 'Dành cho SEO'));
		$form->layout->addGroup('special', array(
			'title' => 'Cấu hình đặc biệt',
			'toggle' => true,
			'hide' => true,
			'ext' => array(
				'style' => "display:".($admin_page!=1?'block':'none')
			)
		));
		
		//add form main
		$form->layout->addItem('title', array(
			'type'	=> 'text',
			'title' => 'Tiêu đề SEO',
			'required' => true,
			'value' => Url::getParam('title', $form->page['title'])
		), 'main');
		$ext = array();
		if($form->page['no_delete'] && $data['mode']!='copy'){
			$ext = array('readonly' => 'readonly');
		}
		$form->layout->addItem('name', array(
			'type'	=> 'text',
			'title' => 'URL',
			'required' => true,
			'value' => Url::getParam('name', $form->page['name']),
			'ext' => $ext
		), 'main');
		$form->layout->addItem('rewrite', array(
			'type'	=> 'text',
			'title' => 'URL Rewrite',
			'value' => Url::getParam('rewrite', $form->page['rewrite']),
			'ext' => $ext
		), 'main');
		$form->layout->addItem('admin', array(
			'type'	=> 'checkbox',
			'title' => 'Trang quản trị (admin)',
			'style' => 'onoff',
			'checked' => $admin_page == 1,
			'ext' => array(
				'onchange' => "shop.admin.page.changeType(this.checked)"
			)
		), 'main');
		$form->layout->addItem('layout', array(
			'type'	=> 'select',
			'title' => 'Layout website',
			'required' => true,
			'options' => FunctionLib::getOption($data['layoutOptions'], Url::getParam('layout',$form->page['layout']))
		), 'main');
		if($form->page['layout']){
			$form->layout->addItem('old_layout', array(
				'type'	=> 'hidden',
				'save' => false,
				'value' => $form->page['layout']
			), 'main');
		}
		if(!empty($data['layoutMobileOptions'])){
			$form->layout->addItem('layout_mobile', array(
				'type'	=> 'select',
				'title' => 'Layout mobile',
				'required' => $admin_page != 1,
				'options' => FunctionLib::getOption($data['layoutMobileOptions'], Url::getParam('layout_mobile',$form->page['layout_mobile']))
			), 'main');
			if($form->page['layout_mobile']){
				$form->layout->addItem('old_layout_mobile', array(
					'type'	=> 'hidden',
					'save' => false,
					'value' => $form->page['layout_mobile']
				), 'main');
			}
		}else{
			$form->layout->addItem('old_layout_mobile', array(
				'type'	=> 'html',
				'html' => '<div><em>Bấm <a href="'.Url::build('themes').'" target="_blank">vào đây</a> để kích hoạt theme mobile</em></div>',
			), 'main');
		}

		$form->layout->addItem('keyword', array(
			'type'	=> 'textarea',
			'title' => 'Từ khóa',
			'editor'=> false,
			'value' => Url::getParam('keyword', $form->page['keyword']),
			'ext' => array(
				'rows' => 5,
				'cols' => 60
			)
		), 'seo');
		$form->layout->addItem('description', array(
			'type'	=> 'textarea',
			'title' => 'Mô tả',
			'editor'=> false,
			'value' => Url::getParam('description', $form->page['description']),
			'ext' => array(
				'rows' => 5,
				'cols' => 60
			)
		), 'seo');
		
		$themeOptions = array();
		$themeExtOptions = array();
		foreach($data['theme_web'] as $tit){
			$themeOptions[$tit] = $tit == 'sys' ? 'Không thiết lập (Tự ăn theo theme mặc định)' : $tit;
			$themeExtOptions[$tit] = array(
				'onclick' => "shop.admin.page.click('".$tit."', '')"
			);
		}
		$form->layout->addItem('themes', array(
			'type'	=> 'radio-group',
			'style' => 1,
			'title' => 'Page chỉ xuất hiện ở themes website',
			'value' => $data['mode'] == 'copy' ? $form->page['themes'] : $form->default_theme,
			'options' => $themeOptions,
			'ext' => $themeExtOptions
		), 'special');

		if(!empty($data['layoutMobileOptions'])){
			$themeOptions = array();
			$themeExtOptions = array();
			foreach($data['theme_mobile'] as $tit){
				$themeOptions[$tit] = $tit == 'no_mobile' ? 'Không thiết lập (Tự ăn theo theme mặc định)' : $tit;
				$themeExtOptions[$tit] = array(
					'onclick' => "shop.admin.page.click('','".$tit."')"
				);
			}
			$form->layout->addItem('themes_mobile', array(
				'type'	=> 'radio-group',
				'style' => 1,
				'title' => 'Page chỉ xuất hiện ở themes mobile',
				'value' => $data['mode'] == 'copy' ? $form->page['themes_mobile'] : $form->default_theme_mobile,
				'options' => $themeOptions,
				'ext' => $themeExtOptions
			), 'special');
		}
		
		if($action == 'draw'){
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
}