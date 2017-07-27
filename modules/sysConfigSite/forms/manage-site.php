<?php
class ManageSiteForm extends Form {
	var $siteConf, $max_upload_size = 0,
	$key 		= 'site_configs',
	$keywords	= 'site_keywords',
	$description= 'site_description',
	$black_ips  = 'black_ips',
	$imgSize 	= 'imageSize',
	$imgServer 	= 'imageServer';

	function __construct() {
		parent::__construct();
		$this->siteConf = ConfigSite::getConfigFromDB($this->key, array(),true);
		$this->siteConf['siteVer']	= CGlobal::$version;
		$this->siteConf['black_ips']	= CGlobal::$black_ips;
		$this->siteConf['keywords'] 	= CGlobal::$keywords;
		$this->siteConf['description']	= CGlobal::$meta_desc;
	
		$this->max_upload_size = substr(ini_get('upload_max_filesize'), 0, -1);
		$this->max_upload_size = intval($this->max_upload_size);
		
		//khoi tao toan bo config
		if(isset(CGlobal::$configs['site_module_config']) && CGlobal::$configs['site_module_config'] == 1){
			ModuleConfig::run();
		}

		$this->link_js_me('admin_config.js', __FILE__);
		//$this->link_js("plugins/ckeditor/ckeditor.js");
		//Gallery::addMultiUploadCore($this, 'multiupload.js');
	}
	
	function draw(){
		$water_mark = Url::getParam('water_mark', isset($this->siteConf['water_mark'])?$this->siteConf['water_mark']:'');
		$water_mark_opts = array(
			'' => '-- Mặc định --',
			'topleft' => 'Phía trên bên trái',
			'topright' => 'Phía trên bên phải',
			'bottomleft' => 'Phía dưới bên trái',
			'bottomright' => 'Phía dưới bên phải',
			'center' => 'Ở giữa ảnh'
		);
		$water_mark_opts = FunctionLib::getOption($water_mark_opts, $water_mark);

		$config = array(
			'multiupload'	=>	Url::getParam('multiupload', $this->siteConf['multiupload']),
			'domain_name'	=>	Url::getParam('domain_name', $this->siteConf['domain_name']),
			'site_name'		=>	Url::getParam('site_name', $this->siteConf['site_name']),
			'email'			=>	Url::getParam('email', $this->siteConf['email']),
			'logo_title'	=>	Url::getParam('logo_title', $this->siteConf['logo_title']),
			'logo'			=>	Url::getParam('old_logo', $this->siteConf['logo']),
			'siteVer'			=>	Url::getParam('siteVer', $this->siteConf['siteVer']),
			'favicon'		=>	Url::getParam('old_favicon', $this->siteConf['favicon']),
			'background'	=>	Url::getParam('old_background', $this->siteConf['background']),
			'alert'			=>	Url::getParam('old_alert', $this->siteConf['alert']),
			'alert_txt'		=>	Url::getParam('alert_txt', $this->siteConf['alert_txt']),
			'currency'		=>	Url::getParam('currency', $this->siteConf['currency']),
			'change_pass'	=>	Url::getParam('change_pass', $this->siteConf['change_pass']),
			'relogin'		=>	Url::getParam('relogin', $this->siteConf['relogin']),
			'log2step'		=>	Url::getParam('log2step', $this->siteConf['log2step']),
			'log2step_time'	=>	Url::getParam('log2step_time', $this->siteConf['log2step_time']),
			'captcha'		=>	Url::getParam('captcha', $this->siteConf['captcha']),
			'captcha_error'	=>	Url::getParam('captcha_error', $this->siteConf['captcha_error']),
			'captcha_public'=>	Url::getParam('captcha_public', $this->siteConf['captcha_public']),
			'captcha_private'=> Url::getParam('captcha_private', $this->siteConf['captcha_private']),
			'upload_size'	=>	Url::getParam('upload_size', $this->siteConf['upload_size']),
			'website_status'=>	Url::getParam('website_status', $this->siteConf['website_status']),
			'keywords'      =>	Url::getParam('keywords', $this->siteConf['keywords']),
			'description'   =>	Url::getParam('description', $this->siteConf['description']),
            'img_fix'       =>	Url::getParamInt('img_fix', isset($this->siteConf['img_fix'])?$this->siteConf['img_fix']:100),
			'img_genauto'   =>	Url::getParamInt('img_genauto', isset($this->siteConf['img_genauto'])?$this->siteConf['img_genauto']:1),
			'water_mark_min'=>	Url::getParamInt('water_mark_min', isset($this->siteConf['water_mark_min'])?$this->siteConf['water_mark_min']:200),
			'water_mark_active'	=>	Url::getParam('water_mark_active', $this->siteConf['water_mark_active']),
			'water_mark'    =>	$water_mark_opts,
			'water_mark_img'=>	Url::getParam('old_water_mark_img', $this->siteConf['water_mark_img']),
			'water_mark_margin'=>	Url::getParamInt('water_mark_margin', isset($this->siteConf['water_mark_margin'])?$this->siteConf['water_mark_margin']:5),
			'water_mark_trans'=>	Url::getParamInt('water_mark_trans', isset($this->siteConf['water_mark_trans'])?$this->siteConf['water_mark_trans']:30),
			'black_ips'   	=>	Url::getParam('black_ips', $this->siteConf['black_ips']),
			'link_facebook'	=>	Url::getParam('link_facebook', $this->siteConf['link_facebook']),
			'link_youtube'	=>	Url::getParam('link_youtube', $this->siteConf['link_youtube']),
			'link_in'		=>	Url::getParam('link_in', $this->siteConf['link_in']),
			'link_google'	=>	Url::getParam('link_google', $this->siteConf['link_google']),
			'link_twitter'	=>	Url::getParam('link_twitter', $this->siteConf['link_twitter']),
			'link_pinterest'=>	Url::getParam('link_pinterest', $this->siteConf['link_pinterest']),
			'hotline'		=>	Url::getParam('hotline', $this->siteConf['hotline']),
			'address'		=>	Url::getParam('address', $this->siteConf['address']),
			'ga'			=>	Url::getParam('ga', $this->siteConf['ga']),
			'smtp_host'		=>	Url::getParam('smtp_host', $this->siteConf['smtp_host']),
			'smtp_port'		=>	Url::getParam('smtp_port', $this->siteConf['smtp_port']),
			'smtp_secure'	=>	Url::getParam('smtp_secure', $this->siteConf['smtp_secure']),
			'smtp_user'		=>	Url::getParam('smtp_user', $this->siteConf['smtp_user']),
			'smtp_pass'		=>	Url::getParam('smtp_pass', $this->siteConf['smtp_pass']),
			'smtp_from'		=>	Url::getParam('smtp_from', $this->siteConf['smtp_from']),
			'smtp_debug'	=>	Url::getParamInt('smtp_debug', $this->siteConf['smtp_debug']),
			'smtp_dev'		=>	Url::getParamInt('smtp_dev', $this->siteConf['smtp_dev']),
			'save_log'		=>	Url::getParamInt('save_log', $this->siteConf['save_log'])
		);
		
		$config['logo_img'] = ImageUrl::getSiteLogo($config['logo']);
		$config['background_img'] = ImageUrl::getSiteBG($config['background']);
		$config['alert_img'] = ImageUrl::getSiteBG($config['alert'] == '' ? DEFAULT_SITE_STOP : $config['alert']);
		$config['favicon_img'] = ImageUrl::getSiteFavicon($config['favicon']);
		$config['water_mark_source'] = ImageUrl::getWaterMask($config['water_mark_img']);
		$config['water_mark_def'] =	$water_mark;
		
		$per = array(
			"site" => User::user_access('config site'),
			"image" => User::user_access('config image'),
			"security" => User::user_access('config security'),
		);
		$tab = Url::getParam('tab','');
		$dataForm = array();
		if($per['site']){
			array_push($dataForm, $this->createTab('basic', 'Thông tin', 'dvr', $this->formBasic($config), $tab == 'basic'));
			array_push($dataForm, $this->createTab('imageWebsite', 'Hình ảnh', 'image', $this->formImageWebsite($config), $tab == 'imageWebsite'));
		}
		if($per['image']){
			array_push($dataForm, $this->createTab('fileImage', 'File & Ảnh', 'perm_media', $this->formFileImage($config), $tab == 'fileImage'));
		}
		if($per['site']){
			array_push($dataForm, $this->createTab('social', 'Social', 'share', $this->formSocial($config), $tab == 'social'));
			array_push($dataForm, $this->createTab('smtp', 'SMTP', 'email', $this->formSMTP($config), $tab == 'smtp'));
			array_push($dataForm, $this->createTab('control', 'Vận hành', 'broken_image', $this->formControl($config), $tab == 'control'));
		}
		if($per['security']){
			array_push($dataForm, $this->createTab('secure', 'Bảo mật', 'security', $this->formSecure($config), $tab == 'secure'));
		}
		if(!empty(ConfigSite::$configs)){
			//System::debug(ConfigSite::$configs);
			array_push($dataForm, $this->createTab('moduleConf', 'Module', 'settings_applications', $this->formModuleConf(ConfigSite::$configs), $tab == 'moduleConf'));
		}
		if($per['site']){
			array_push($dataForm, $this->createTab('otherConf', 'Cấu hình khác', 'settings', $this->formOtherConf($config), $tab == 'otherConf'));
			array_push($dataForm, $this->createTab('global', 'Biến toàn cục', 'language', $this->formGlobal($config), $tab == 'global'));
		}
		
		$this->layout->init(array(
			'style' => 'html',
			'upload'=> true
		));
		$html = $this->layout->genTabAuto($dataForm);
		$html.= '<div class="m-b-20">';
		$html.= $this->layout->genButtonAuto(array(
			'title' => 'Lưu thay đổi',
            'style' => 0,
            'color' => 'success',
            'icon'  => 'done',
            'type'  => 1,
            'size'  => 1,
            'ext' => array(
                'onclick' => 'shop.admin.manage_site.submit(document.'.$this->name.')'
            )
		));
		$html.= $this->layout->genButtonAuto(array(
			'title' => 'Hủy bỏ',
            'style' => 0,
            'color' => 'danger',
            'icon'  => 'clear',
            'type'  => 1,
            'size'  => 1,
            'ext' => array(
                'onclick' => 'history.go(-1)',
				'style' => 'margin-left:20px'
            )
		));
		$html.='</div>';
		$this->layout->genFormAuto($this, array(
			'html' => $html
		));
    }
	
	function tableConfig($def){
		$group_key = 'main';
		$basic = new Form('tableConfigForm');
		$basic->layout->init(array('style' => 'grid'));
		$basic->layout->addItemView('name', array('title' => 'Tên thư mục'));
		$basic->layout->addItemView('defined', array('title' => 'Định nghĩa'));
		$basic->layout->addItemView('wm', array(
			'title' => 'Water Mask',
			'type'  => 'icon',
			'only'	=> true,
			'head' => array(
				'width' => 120
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$basic->layout->addItemView('size', array('title' => 'Kích thước'));
		$basic->layout->addItemView('edit', array(
			'title' => 'Sửa',
			'type'  =>	'icon',
			'icon' => 'mode_edit',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$basic->layout->addItemView('delete', array(
			'title' => 'Xóa',
			'type'  =>	'icon',
			'icon' => 'delete',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		foreach($def as $k => $v){
			$v['edit'] = "javascript:shop.admin.manage_site.newImgConfig('".$v['name']."', '".$v['defined']."', ".($v['wm'] ? $v['wm'] : 0).")";
			$v['delete'] = "javascript:shop.admin.manage_site.delImgConfig('".$v['name']."')";
			$v['wm'] = array(
				'icon' => 'check_circle',
				'color'=> ($v['wm'] == 1) ? '' : 'grey'
			);
			$v['size'] = '<div class="m-b-10">'.$basic->layout->genButtonAuto(array(
				'style'=> 0,
				'type' => 1,
				'size' => 3,
				'color'=> 'success',
				'icon' => 'add',
				'ext'  => array(
					'onclick' => "shop.admin.manage_site.newImgSize('".$v['name']."')"
				)
			)).'</div>';
			if(!empty($v['sizes'])){
				$v['size'].= $this->sizeTable($v['name'], $v['sizes']);
			}
			$def[$k] = $v;
		}
		return $basic->layout->genFormAuto($basic, array(
			'items' => $def
		), true);
	}
	
	function sizeTable($name, $def){
		$group_key = 'main';
		$basic = new Form('sizeTableForm');
		$basic->layout->init(array('style' => 'grid'));
		$basic->layout->addItemView('w', array('title' => 'Chiều rộng'));
		$basic->layout->addItemView('h', array('title' => 'Chiều cao'));
		$basic->layout->addItemView('delete', array(
			'title' => 'Xóa',
			'type'  =>	'icon',
			'icon' => 'delete',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		foreach($def as $k => $v){
			$v['delete'] = "javascript:shop.admin.manage_site.delImgSize('$name', $k)";
			$def[$k] = $v;
		}
		return $basic->layout->genFormAuto($basic, array(
			'items' => $def
		), true);
	}
	
	function formFileImage($def){
		$group_key = 'main';
		$basic = new Form('fileImageForm');
		$basic->layout->addItem('upload_size', array(
			'type' => 'text',
			'title'=> 'Dung lượng file Upload',
			'value'=> isset($def['upload_size']) ? $def['upload_size'] : '',
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => "3"
			),
			'caption' => 'Đơn vị MB (Tối đa <b>'.$this->max_upload_size.' MB</b>)'
		), $group_key);
		$basic->layout->addItem('img_fix', array(
			'type' => 'text',
			'title'=> 'Chất lượng ảnh',
			'value'=> isset($def['img_fix']) ? $def['img_fix'] : 100,
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => "3"
			),
			'caption' => 'Đơn vị % (<em class="col-red">Không nên để thấp hơn 80</em>)'
		), $group_key);
		$basic->layout->addItem('img_genauto', array(
			'type' => 'checkbox',
			'label'=> 'Sinh ảnh tự động',
			'style'=> 'onoff',
			'label_pos' => 'left',
			'checked'=> isset($def['img_genauto']) && $def['img_genauto'] == 1,
			'caption' => '<em class="col-red">(Giúp tối ưu lưu trữ, tự sinh ảnh theo kích thước nếu chưa có)</em>'
		), $group_key);
		//chen them
		
		$html = '<div class="clearfix"><h2 class="card-inside-title">Thư mục, Kích thước ảnh</h2><div class="input-group"><div class="form-line p-b-10">';
		$html.= $basic->layout->genButtonAuto(array(
			'style'=> 0,
			'type' => 1,
			'title'=> 'Thêm cấu hình',
			'color'=> 'success',
			'icon' => 'add',
			'ext'  => array(
				'onclick' => "shop.admin.manage_site.newImgConfig()"
			)
		));
		$html.= $basic->layout->genButtonAuto(array(
			'style'=> 0,
			'type' => 1,
			'title'=> 'Ghi cấu hình',
			'color'=> 'info',
			'icon' => 'save',
			'ext'  => array(
				'onclick' => "shop.admin.manage_site.build()",
				'style' => 'margin-left:30px'
			)
		));
		$html.= $basic->layout->genButtonAuto(array(
			'style'=> 0,
			'type' => 1,
			'title'=> 'Xóa toàn bộ ảnh',
			'color'=> 'danger',
			'icon' => 'delete_forever',
			'ext'  => array(
				'onclick' => "shop.admin.manage_site.delImages()",
				'style' => 'margin-left:30px'
			)
		));
		$html.= '<div class="m-t-20 m-b-20 col-red"><b>Chú ý:</b> <em>Muốn co theo chiều rộng thì chiều cao set = 0, Muốn co theo chiều cao thì chiều rộng set = 0. Nếu nhập cả 2 thì sẽ tự động co ảnh vào trong khung đó.</em></div>';
		$imgSize = ConfigSite::getConfigFromDB($this->imgSize, array(), true);
		if(!empty($imgSize)){
			$html.= $this->tableConfig($imgSize);
		}
		$html.= '</div></div></div>';
		$basic->layout->addItem('fileSizeCon', array(
			'type' => 'html',
			'html' => $html
		), $group_key);
		
		$basic->layout->addItem('water_mark_active', array(
			'type' => 'checkbox',
			'label'=> 'Water Mask',
			'style'=> 'onoff',
			'label_pos' => 'left',
			'checked'=> isset($def['water_mark_active']) && $def['water_mark_active'] == 1,
			'ext' => array(
				'onchange' => "$('#waterMaskActive').toggle()"
			)
		), $group_key);
		$basic->layout->addItem('waterMaskCon', array(
			'type' => 'html',
			'html' => '<div class="m-t-10'.($def['water_mark_active'] == 0 ? ' hide_me' : '').'" id="waterMaskActive">'.$this->formWaterMask($def).'</div>'
		), $group_key);
		
		$basic->layout->addItem('img_server', array(
			'type' => 'radio-group',
			'title'=> 'Nơi lưu trữ',
			'options' => array(
				0 => 'Localhost',
				1 => 'Server ảnh'
			),
			'value' => isset($def['img_server']) ? $def['img_server'] : 0,
			'ext' => array(
				0 => array(
					'onchange' => "$('#imgServerActive').addClass('hide_me')"
				),
				1 => array(
					'onchange' => "$('#imgServerActive').removeClass('hide_me')"
				)
			),
			'line' => false
		), $group_key);
		$imageServer = ConfigSite::getConfigFromDB($this->imgServer, array(), true);
		$basic->layout->addItem('serverCon', array(
			'type' => 'html',
			'html' => '<div class="m-t-10'.($imageServer['img_server'] == 0 ? ' hide_me' : '').'" id="imgServerActive">'.$this->formImgServer($imageServer).'</div>'
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formWaterMask($def){
		$group_key = 'main';
		$basic = new Form('waterForm');
		$basic->layout->addItem('water_mark_min', array(
			'type' => 'text',
			'title'=> 'Không áp dụng với ảnh có chiều rộng nhỏ hơn',
			'value'=> isset($def['water_mark_min']) ? $def['water_mark_min'] : '',
			'caption' => 'Đơn vị <b class="col-red">Pixel</b>',
			'number' => true,
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => 3
			)
		), $group_key);
		$basic->layout->addItem('water_mark_img', array(
			'type' => 'file',
			'title'=> 'Ảnh Water Mask',
			'old'=> array(
				'id' => 'old_water_mark_img',
				'value' => isset($def['water_mark_img']) ? $def['water_mark_img'] : '',
				'src' => $def['water_mark_source'],
				'ext' => array(
					'style' => 'border:2px dashed #ccc;padding:2px',
					'height'=> 50
				)
			),
			'ext' => array(
				'change' => "shop.checkbox.change('mask_default', false)"
			)
		), $group_key);
		$basic->layout->addItem('mask_default', array(
			'type' => 'checkbox',
			'save' => false,
			'label'=> 'Water Mask mặc định',
			'style' => 'onoff',
			'label_pos' => 'left',
			'checked' => $def['water_mark_img'] == ''
		), $group_key);
		$basic->layout->addItem('water_mark', array(
			'type' => 'select',
			'title'=> 'Vị trí Water mask',
			'options'=> $def['water_mark']
		), $group_key);
		$basic->layout->addItem('water_mark_margin', array(
			'type' => 'text',
			'number' => true,
			'title'=> 'Khoảng cách với lề',
			'value'=> isset($def['water_mark_margin']) ? $def['water_mark_margin'] : '',
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => 3
			)
		), $group_key);
		$basic->layout->addItem('water_mark_trans', array(
			'type' => 'text',
			'number' => true,
			'title'=> 'Độ trong nền',
			'value'=> isset($def['water_mark_trans']) ? $def['water_mark_trans'] : '',
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => 3
			)
		), $group_key);

		if(!empty($def['water_mark_source'])){
			$basic->layout->addItem('serverCon', array(
			'type' => 'html',
				'html' => '<div class="demo_wm m-b-30"><img src="'.$def['water_mark_source'].'" height="30" class="'.$def['water_mark_def'].'" style="'.($def['water_mark_def'] != 'center' ? 'padding:'.$def['water_mark_margin'].'px;' : '').'opacity:'.($def['water_mark_trans']/100).'" /></div>'
			), $group_key);
		}

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formImgServer($def){
		$group_key = 'main';
		$basic = new Form('imageSerOnForm');
		$basic->layout->addItem('ftp_host', array(
			'type' => 'text',
			'title'=> 'FTP Host',
			'value'=> isset($def['ftp_host']) ? $def['ftp_host'] : ''
		), $group_key);
		$basic->layout->addItem('ftp_user', array(
			'type' => 'text',
			'title'=> 'FTP Username',
			'value'=> isset($def['ftp_user']) ? $def['ftp_user'] : ''
		), $group_key);
		$basic->layout->addItem('ftp_pass', array(
			'type' => 'password',
			'title'=> 'FTP Password',
			'value'=> isset($def['ftp_pass']) ? $def['ftp_pass'] : ''
		), $group_key);
		$basic->layout->addItem('img_domain', array(
			'type' => 'text',
			'title'=> 'Domain',
			'value'=> isset($def['img_domain']) ? $def['img_domain'] : ''
		), $group_key);
		$basic->layout->addItem('img_tmp_dir', array(
			'type' => 'text',
			'title'=> 'Thư mục chứa code',
			'value'=> isset($def['img_tmp_dir']) ? $def['img_tmp_dir'] : ''
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formCaptcha($def){
		$group_key = 'main';
		$basic = new Form('captchaForm');
		$basic->layout->addItem('captcha_error', array(
			'type' => 'text',
			'number' => true,
			'title'=> 'Số lần nhập sai cho phép',
			'value'=> isset($def['captcha_error']) ? $def['captcha_error'] : '',
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => "2"
			)
		), $group_key);
		$basic->layout->addItem('captcha_public', array(
			'type' => 'text',
			'title'=> 'Public Key',
			'value'=> isset($def['captcha_public']) ? $def['captcha_public'] : ''
		), $group_key);
		$basic->layout->addItem('captcha_private', array(
			'type' => 'text',
			'title'=> 'Private Key',
			'value'=> isset($def['captcha_private']) ? $def['captcha_private'] : ''
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formLog2Step($def){
		$group_key = 'main';
		$basic = new Form('log2StepForm');
		$basic->layout->addItem('log2step_time', array(
			'type' => 'text',
			'number' => true,
			'title'=> 'Thời gian hiệu lực 2FA',
			'value'=> isset($def['log2step_time']) ? $def['log2step_time'] : '',
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => "2"
			)
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formSecure($def){
		$group_key = 'main';
		$basic = new Form('secureForm');
		$basic->layout->addItem('captcha', array(
			'type' => 'checkbox',
			'label'=> 'Captcha nếu sai mật khẩu',
			'style'=> 'onoff',
			'label_pos' => 'left',
			'checked'=> isset($def['captcha']) && $def['captcha'] == 1,
			'ext' => array(
				'onchange' => "$('#captcha_active').toggle()"
			)
		), $group_key);
		$basic->layout->addItem('captchaCon', array(
			'type' => 'html',
			'html' => '<div class="m-t-10'.($def['captcha'] == 0 ? ' hide_me' : '').'" id="captcha_active">'.$this->formCaptcha($def).'</div>'
		), $group_key);
		$basic->layout->addItem('log2step', array(
			'type' => 'checkbox',
			'label'=> 'Đăng nhập 2 bước (2FA)',
			'style'=> 'onoff',
			'label_pos' => 'left',
			'checked'=> isset($def['log2step']) && $def['log2step'] == 1,
			'ext' => array(
				'onchange' => "$('#log2step_active').toggle()"
			)
		), $group_key);
		$basic->layout->addItem('log2Con', array(
			'type' => 'html',
			'html' => '<div class="m-t-10'.($def['log2step'] == 0 ? ' hide_me' : '').'" id="log2step_active">'.$this->formLog2Step($def).'</div>'
		), $group_key);
		$basic->layout->addItem('relogin', array(
			'type' => 'text',
			'number' => true,
			'title'=> 'Đăng nhập lại sau',
			'value'=> isset($def['relogin']) ? $def['relogin'] : '',
			'caption' => 'Số ngày hiệu lực của đăng nhập tự động (<em class="col-red">Nhập 0 nếu muốn tắt chức năng này</em>)',
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => "2"
			)
		), $group_key);
		$basic->layout->addItem('change_pass', array(
			'type' => 'text',
			'number' => true,
			'title'=> 'Đổi mật khẩu sau',
			'value'=> isset($def['change_pass']) ? $def['change_pass'] : '',
			'caption' => 'Số ngày hiệu lực của mật khẩu (<em class="col-red">Nhập 0 nếu muốn tắt chức năng này</em>)',
			'ext' => array(
				'onkeypress' => "return shop.numberOnly(this, event)",
				'maxlength' => "2"
			)
		), $group_key);
		$basic->layout->addItem('black_ips', array(
			'type' => 'textarea',
			'title'=> 'Block Black IP',
			'value'=> isset($def['black_ips']) ? $def['black_ips'] : '',
			'ext'  => array(
				'rows' => 5
			),
			'caption' => '<em>Dùng để chặn các địa chỉ IP xấu không cho truy cập vào site</em><div class="col-red"><b>Lưu ý:</b> <em>Mỗi địa chỉ IP nằm trên 1 hàng</em></div>'
		), $group_key);
		

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formImageAlert($def){
		$group_key = 'main';
		$basic = new Form('imageAlertForm');
		$basic->layout->addItem('alert_txt', array(
			'type' => 'text',
			'title'=> 'Thông báo',
			'value'=> isset($def['alert_txt']) ? $def['alert_txt'] : ''
		), $group_key);
		$basic->layout->addItem('alert', array(
			'type' => 'file',
			'title'=> 'Ảnh thông báo',
			'old'=> array(
				'id' => 'old_alert',
				'value' => isset($def['alert']) ? $def['alert'] : '',
				'src' => $def['alert_img'],
				'ext' => array(
					'style' => 'border:2px dashed #ccc;padding:2px',
					'height'=> 50
				)
			),
			'ext' => array(
				'change' => "shop.checkbox.change('alert_default', false)"
			)
		), $group_key);
		$basic->layout->addItem('alert_default', array(
			'type' => 'checkbox',
			'label'=> 'Ảnh thông báo mặc định (700x400)',
			'style' => 'onoff',
			'save' => false,
			'label_pos' => 'left',
			'checked' => $def['alert'] == '',
			'line' => false
		), $group_key);
		
		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formControl($def){
		$group_key = 'main';
		$basic = new Form('controlForm');
		$basic->layout->addItem('save_log', array(
			'type' => 'checkbox',
			'label'=> 'Hệ thống LOG',
			'style' => 'onoff',
			'save' => false,
			'label_pos' => 'left',
			'checked' => isset($def['save_log']) && $def['save_log'] == 1,
			'caption' => '<em class="col-red">Nên tắt để hệ thống chạy ổn định hơn</em>'
		), $group_key);
		
		$basic->layout->addItem('website_status', array(
			'type' => 'radio-group',
			'title'=> 'Tình trạng Website',
			'options' => array(
				'close' => 'Thông báo nghỉ',
				'offline' => 'Đang sửa chữa',
				'online' => 'Hoạt động'
			),
			'value' => isset($def['website_status']) ? $def['website_status'] : 'online',
			'ext' => array(
				'close' => array(
					'onchange' => "shop.openCloseStatus('status_close')"
				),
				'offline' => array(
					'onchange' => "shop.openCloseStatus('status_on')"
				),
				'online' => array(
					'onchange' => "shop.openCloseStatus('status_off')"
				)
			)
		), $group_key);
		$basic->layout->addItem('thongbao', array(
			'type' => 'html',
			'html' => '<div class="m-t-10'.($def['website_status'] != 'close' ? ' hide_me' : '').'" id="thongbaonghi">'.$this->formImageAlert($def).'</div>'
		), $group_key);
	

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formSMTP($def){
		$group_key = 'main';
		$basic = new Form('smtpForm');
		$basic->layout->addItem('smtp_host', array(
			'type' => 'text',
			'title'=> 'SMTP Host',
			'value'=> isset($def['smtp_host']) ? $def['smtp_host'] : ''
		), $group_key);
		$basic->layout->addItem('smtp_port', array(
			'type' => 'text',
			'title'=> 'SMTP Port',
			'value'=> isset($def['smtp_port']) ? $def['smtp_port'] : ''
		), $group_key);
		$basic->layout->addItem('smtp_secure', array(
			'type' => 'select',
			'title'=> 'SMTP Secure',
			'options'=> FunctionLib::getOption(array('' => 'None', 'tls' => 'TLS', 'ssl' => 'SSL'), isset($def['smtp_secure']) ? $def['smtp_secure'] : '')
		), $group_key);
		$basic->layout->addItem('smtp_user', array(
			'type' => 'text',
			'title'=> 'SMTP User',
			'value'=> isset($def['smtp_user']) ? $def['smtp_user'] : ''
		), $group_key);
		$basic->layout->addItem('smtp_pass', array(
			'type' => 'password',
			'title'=> 'SMTP Password',
			'value'=> isset($def['smtp_pass']) ? $def['smtp_pass'] : ''
		), $group_key);
		$basic->layout->addItem('smtp_from', array(
			'type' => 'text',
			'title'=> 'From Email',
			'value'=> isset($def['smtp_from']) ? $def['smtp_from'] : ''
		), $group_key);
		$basic->layout->addItem('smtp_debug', array(
			'type' => 'checkbox',
			'label'=> 'SMTP Debug',
			'style'=> 'onoff',
			'label_pos' => 'left',
			'checked' => isset($def['smtp_debug']) && $def['smtp_debug'] == 2
		), $group_key);
		$basic->layout->addItem('smtp_debug', array(
			'type' => 'checkbox',
			'label'=> 'SMTP Debug',
			'style'=> 'onoff',
			'label_pos' => 'left',
			'checked' => isset($def['smtp_debug']) && $def['smtp_debug'] == 2,
			'value' => 2
		), $group_key);
		$basic->layout->addItem('smtp_dev', array(
			'type' => 'checkbox',
			'label'=> 'Dev Mode (Not send email)',
			'style'=> 'onoff',
			'label_pos' => 'left',
			'checked' => isset($def['smtp_dev']) && $def['smtp_dev'] == 1,
			'line' => false
		), $group_key);
	

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formModuleConf($conf){
		$group_key = 'main';
		$basic = new Form('moduleConfForm');
		$max = count($conf);
		$counter = 0;
		foreach($conf as $k => $c){
			$counter ++;
			$c['line'] = $counter < $max;
			if(isset($c['val'])){
				$c['value'] = $c['val'];
			}
			if(isset($c['opt'])){
				$c['options'] = $c['opt'];
			}
			if($c['type'] == 'radio' || $c['type'] == 'checkbox'){
				$c['checked'] = $c['value'] == 1;
			}
			$basic->layout->addItem($k, $c, $group_key);
		}

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formGlobal($def){
		$group_key = 'main';
		$basic = new Form('globalForm');
		$basic->layout->addItem('viewGlobal', array(
			'type' => 'button',
			'title'=> 'Xem biến toàn cục',
			'color'=> 'green',
			'icon' => 'visibility',
			'ext'  => array(
				'onclick' => "shop.admin.manage_site.getCGlobal()"
			)
		), $group_key);
		$basic->layout->addItem('globalCon', array(
			'type' => 'html',
			'html' => '<div class="cglobal m-t-10"></div>'
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formOtherConf($def){
		$group_key = 'main';
		$basic = new Form('otherConfForm');
		$basic->layout->addItem('siteVer', array(
			'type' => 'text',
			'title'=> 'Phiên bản JS & CSS',
			'value'=> isset($def['siteVer']) ? $def['siteVer'] : ''
		), $group_key);
		$basic->layout->addItem('addVer', array(
			'type' => 'button',
			'title'=> 'Tăng phiên bản',
			'color'=> 'green',
			'icon' => 'add',
			'ext'  => array(
				'onclick' => "shop.admin.manage_site.increJsVersion('siteVer')"
			)
		), $group_key);
		$basic->layout->addItem('currency', array(
			'type' => 'text',
			'title'=> 'Đơn vị tiền tệ',
			'value'=> isset($def['currency']) ? $def['currency'] : ''
		), $group_key);
		$basic->layout->addItem('multiupload', array(
			'type' => 'radio-group',
			'title'=> 'MultiUpload Core',
			'options'=> array(
				0 => 'Flash',
				1 => 'HTML 5'
			),
			'value' => isset($def['multiupload']) ? $def['multiupload'] : 0
		), $group_key);
		$basic->layout->addItem('ga', array(
			'type' => 'textarea',
			'title'=> 'Google Analytics',
			'value'=> isset($def['ga']) ? $def['ga'] : '',
			'ext' => array(
				'rows' => 5
			)
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formBasic($def){
		$group_key = 'main';
		$basic = new Form('basicForm');
		$basic->layout->addItem('domain_name', array(
			'type' => 'text',
			'title'=> 'Tên Domain',
			'value'=> isset($def['domain_name']) ? $def['domain_name'] : ''
		), $group_key);
		$basic->layout->addItem('site_name', array(
			'type' => 'text',
			'title'=> 'Tiêu đề website',
			'value'=> isset($def['site_name']) ? $def['site_name'] : ''
		), $group_key);
		$basic->layout->addItem('keywords', array(
			'type' => 'textarea',
			'title'=> 'Từ khóa Website',
			'value'=> isset($def['keywords']) ? $def['keywords'] : '',
			'ext' => array(
				'rows' => 5
			)
		), $group_key);
		$basic->layout->addItem('description', array(
			'type' => 'textarea',
			'title'=> 'Mô tả Website',
			'value'=> isset($def['description']) ? $def['description'] : '',
			'ext' => array(
				'rows' => 5
			)
		), $group_key);
		$basic->layout->addItem('email', array(
			'type' => 'text',
			'title'=> 'Email liên hệ',
			'value'=> isset($def['email']) ? $def['email'] : ''
		), $group_key);
		$basic->layout->addItem('hotline', array(
			'type' => 'text',
			'title'=> 'Hotline',
			'value'=> isset($def['hotline']) ? $def['hotline'] : ''
		), $group_key);
		$basic->layout->addItem('address', array(
			'type' => 'text',
			'title'=> 'Địa chỉ',
			'value'=> isset($def['address']) ? $def['address'] : ''
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formImageWebsite($def){
		$group_key = 'main';
		$basic = new Form('imageWebsiteForm');
		$basic->layout->addItem('logo_title', array(
			'type' => 'text',
			'title'=> 'Tiêu đề Logo',
			'value'=> isset($def['logo_title']) ? $def['logo_title'] : ''
		), $group_key);
		$basic->layout->addItem('logo', array(
			'type' => 'file',
			'title'=> 'Ảnh logo',
			'old'=> array(
				'id' => 'old_logo',
				'value' => isset($def['logo']) ? $def['logo'] : '',
				'src' => $def['logo_img'],
				'ext' => array(
					'style' => 'border:2px dashed #ccc;padding:2px',
					'height'=> 50
				)
			),
			'ext' => array(
				'change' => "shop.checkbox.change('logo_default', false)"
			)
		), $group_key);
		$basic->layout->addItem('logo_default', array(
			'type' => 'checkbox',
			'label'=> 'Logo mặc định',
			'style' => 'onoff',
			'save' => false,
			'label_pos' => 'left',
			'checked' => $def['logo'] == ''
		), $group_key);
		
		$basic->layout->addItem('favicon', array(
			'type' => 'file',
			'title'=> 'Ảnh Favicon',
			'old'=> array(
				'id' => 'old_favicon',
				'value' => isset($def['favicon']) ? $def['favicon'] : '',
				'src' => $def['favicon_img'],
				'ext' => array(
					'style' => 'border:2px dashed #ccc;padding:2px',
					'height'=> 24
				)
			),
			'ext' => array(
				'change' => "shop.checkbox.change('favicon_default', false)"
			)
		), $group_key);
		$basic->layout->addItem('favicon_default', array(
			'type' => 'checkbox',
			'label'=> 'Favicon mặc định',
			'save' => false,
			'style' => 'onoff',
			'label_pos' => 'left',
			'checked' => $def['favicon'] == ''
		), $group_key);
		
		$basic->layout->addItem('background', array(
			'type' => 'file',
			'title'=> 'Ảnh Background',
			'old'=> array(
				'id' => 'old_background',
				'value' => isset($def['background']) ? $def['background'] : '',
				'src' => $def['background_img'],
				'ext' => array(
					'style' => 'border:2px dashed #ccc;padding:2px',
					'height'=> 24
				)
			),
			'ext' => array(
				'change' => "shop.checkbox.change('background_default', false)"
			)
		), $group_key);
		$basic->layout->addItem('background_default', array(
			'type' => 'checkbox',
			'save' => false,
			'label'=> 'Background mặc định',
			'style' => 'onoff',
			'label_pos' => 'left',
			'checked' => $def['background'] == '',
			'line' => false
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function formSocial($def){
		$group_key = 'main';
		$basic = new Form('socialForm');
		$basic->layout->addItem('link_facebook', array(
			'type' => 'text',
			'title'=> 'Facebook',
			'value'=> isset($def['link_facebook']) ? $def['link_facebook'] : ''
		), $group_key);
		$basic->layout->addItem('link_google', array(
			'type' => 'text',
			'title'=> 'Google+',
			'value'=> isset($def['link_google']) ? $def['link_google'] : ''
		), $group_key);
		$basic->layout->addItem('link_twitter', array(
			'type' => 'text',
			'title'=> 'Twitter',
			'value'=> isset($def['link_twitter']) ? $def['link_twitter'] : ''
		), $group_key);
		$basic->layout->addItem('link_youtube', array(
			'type' => 'text',
			'title'=> 'Youtube',
			'value'=> isset($def['link_youtube']) ? $def['link_youtube'] : ''
		), $group_key);
		$basic->layout->addItem('link_pinterest', array(
			'type' => 'text',
			'title'=> 'Pinterest',
			'value'=> isset($def['link_pinterest']) ? $def['link_pinterest'] : ''
		), $group_key);
		$basic->layout->addItem('link_in', array(
			'type' => 'text',
			'title'=> 'LinkIn',
			'value'=> isset($def['link_in']) ? $def['link_in'] : ''
		), $group_key);

		//synform
		$html = '';
		$group = $basic->layout->getGroup($group_key);
		if(!empty($group['items'])){
			foreach($group['items'] as $k => $v){
				$item = $basic->layout->getItem($v);
				if(!empty($item)){
					$html .= $basic->layout->genItemHtml($item);
				}
			}
		}
		return $html;
	}
	
	function createTab($id = '', $title = '', $icon = '', $html = '', $active = false){
		return array(
			'id' => $id,
			'title' => $title,
			'icon' => $icon,
			'html' => $html,
			'active' => $active
		);
	}

    function on_submit(){
		$default_info = array(
			'domain_name'	=>	Url::getParam('domain_name', $this->siteConf['domain_name']),
			'site_name'		=>	Url::getParam('site_name', $this->siteConf['site_name']),
			'email'			=>	Url::getParam('email', $this->siteConf['email']),
			'logo_title'	=>	Url::getParam('logo_title', ''),
			'logo'			=>	Url::getParam('old_logo', $this->siteConf['logo']),
			'siteVer'			=>	Url::getParam('siteVer', $this->siteConf['siteVer']),
			'favicon'		=>	Url::getParam('old_favicon', $this->siteConf['favicon']),
			'background'	=>	Url::getParam('old_background', $this->siteConf['background']),
			'alert'			=>	Url::getParam('old_alert', $this->siteConf['alert']),
			'alert_txt'		=>	Url::getParam('alert_txt', $this->siteConf['alert_txt']),
			'currency'		=>	Url::getParam('currency', $this->siteConf['currency']),
			'change_pass'	=>	Url::getParam('change_pass', $this->siteConf['change_pass']),
			'relogin'		=>	Url::getParam('relogin', $this->siteConf['relogin']),
			'log2step'		=>	Url::getParam('log2step', 0),
			'log2step_time'	=>	Url::getParam('log2step_time', $this->siteConf['log2step_time']),
			'captcha'		=>	Url::getParam('captcha', 0),
			'captcha_error'	=>	Url::getParam('captcha_error', 1),
			'captcha_public'=>	Url::getParam('captcha_public', $this->siteConf['captcha_public']),
			'captcha_private'=> Url::getParam('captcha_private', $this->siteConf['captcha_private']),
			'upload_size'	=>	Url::getParamInt('upload_size', $this->siteConf['upload_size']),
			'website_status'=>	Url::getParam('website_status', $this->siteConf['website_status']),
			'water_mark_active'	=>	Url::getParam('water_mark_active', 0),
			'water_mark'    =>	Url::getParam('water_mark', isset($this->siteConf['water_mark'])?$this->siteConf['water_mark']:''),
			'water_mark_img'=>	Url::getParam('old_water_mark_img', $this->siteConf['water_mark_img']),
			'water_mark_min'=>	Url::getParamInt('water_mark_min', $this->siteConf['water_mark_min']),
            'img_fix'       =>	Url::getParamInt('img_fix', isset($this->siteConf['img_fix'])?$this->siteConf['img_fix']:100),
			'img_genauto'   =>	Url::getParamInt('img_genauto', 0),
			'water_mark_margin'=>	Url::getParamInt('water_mark_margin', isset($this->siteConf['water_mark_margin'])?$this->siteConf['water_mark_margin']:5),
			'water_mark_trans'=>	Url::getParamInt('water_mark_trans', isset($this->siteConf['water_mark_trans'])?$this->siteConf['water_mark_trans']:30),
			'multiupload'	=>	Url::getParam('multiupload', $this->siteConf['multiupload']),
			'link_facebook'	=>	Url::getParam('link_facebook', $this->siteConf['link_facebook']),
			'link_youtube'	=>	Url::getParam('link_youtube', $this->siteConf['link_youtube']),
			'link_google'	=>	Url::getParam('link_google', $this->siteConf['link_google']),
			'link_twitter'	=>	Url::getParam('link_twitter', $this->siteConf['link_twitter']),
			'link_pinterest'=>	Url::getParam('link_pinterest', $this->siteConf['link_pinterest']),
			'link_in'		=>	Url::getParam('link_in', $this->siteConf['link_in']),
			'hotline'		=>	Url::getParam('hotline', $this->siteConf['hotline']),
			'address'		=>	Url::getParam('address', $this->siteConf['address']),
			'ga'			=>	Url::getParam('ga', $this->siteConf['ga']),
			'smtp_host'		=>	Url::getParam('smtp_host', $this->siteConf['smtp_host']),
			'smtp_port'		=>	Url::getParam('smtp_port', $this->siteConf['smtp_port']),
			'smtp_secure'	=>	Url::getParam('smtp_secure', $this->siteConf['smtp_secure']),
			'smtp_user'		=>	Url::getParam('smtp_user', $this->siteConf['smtp_user']),
			'smtp_pass'		=>	Url::getParam('smtp_pass', $this->siteConf['smtp_pass']),
			'smtp_from'		=>	Url::getParam('smtp_from', $this->siteConf['smtp_from']),
			'smtp_debug'	=>	Url::getParamInt('smtp_debug', 0),
			'smtp_dev'		=>	Url::getParamInt('smtp_dev', 0),
			'save_log'      =>  Url::getParamInt('save_log', 0)
		);
		$imgServer = array(
			'img_server'	=>	Url::getParam('img_server', 0),
			'ftp_host'		=>	Url::getParam('ftp_host', isset($this->siteConf['ftp_host']) ? $this->siteConf['ftp_host'] : ''),
			'ftp_user'		=>	Url::getParam('ftp_user', isset($this->siteConf['ftp_user']) ? $this->siteConf['ftp_user'] : ''),
			'ftp_pass'		=>	Url::getParam('ftp_pass', isset($this->siteConf['ftp_pass']) ? $this->siteConf['ftp_pass'] : ''),
			'img_domain'	=>	Url::getParam('img_domain', isset($this->siteConf['img_domain']) ? $this->siteConf['img_domain'] : ''),
			'img_tmp_dir'	=>	Url::getParam('img_tmp_dir', isset($this->siteConf['img_tmp_dir']) ? $this->siteConf['img_tmp_dir'] : '')
		);
		if($default_info['upload_size'] == 0){
			$default_info['upload_size'] = 1;
		}
		if($default_info['upload_size'] > $this->max_upload_size){
			$this->setFormError('', 'Dung lượng upload tối đa phải nhỏ hơn '.$this->max_upload_size);
		}

		if($this->errNum == 0){
            $fileLogo = array(0,0);

			//favicon
			if(isset($_POST['favicon_default'])){
				if($default_info['favicon'] != ''){//xoa file cu
					FileHandler::delete(SITEINFO_FOLDER.$default_info['favicon']);
				}
				$default_info['favicon'] = '';
			}else{
				$favicon = $_FILES['favicon']; //favicon
                if ($favicon['name'] != '') {
                    if (($favicon['size'] > 0) && ($favicon['size'] < CGlobal::$max_upload_size) && FileHandler::isFaviconFile($favicon['name'])) {
                        $fname = FileHandler::getNameByTime($favicon['name']);
                        if (FileHandler::upload($favicon['tmp_name'], SITEINFO_FOLDER . $fname)) {
                            if ($default_info['favicon'] != '') {//xoa file cu
                                FileHandler::delete(SITEINFO_FOLDER . $default_info['favicon']);
                            }
                            $default_info['favicon'] = $fname;
                        }
                    }
                }
			}

            //logo
			if(isset($_POST['logo_default'])){
				if($default_info['logo'] != ''){//xoa file cu
					FileHandler::delete(SITEINFO_FOLDER.$default_info['logo']);
				}
				$default_info['logo'] = '';
			}else{
				$logo = $_FILES['logo'];//logo
				if($logo['name'] != ''){
					if(($logo['size'] > 0) && ($logo['size'] < CGlobal::$max_upload_size) && FileHandler::isImageFile($logo['name'])){
                        $fileLogo = getimagesize($logo['tmp_name']);
						$fname = FileHandler::getNameByTime($logo['name']);
						if(FileHandler::upload($logo['tmp_name'], SITEINFO_FOLDER.$fname)){
							if($default_info['logo'] != ''){//xoa file cu
								FileHandler::delete(SITEINFO_FOLDER.$default_info['logo']);
							}
							$default_info['logo'] = $fname;
						}
					}
				}
			}

            //size logo
            if($default_info['logo'] == ''){
                $fileLogo = @getimagesize(DEFAULT_SITE_LOGO);
            }
            $default_info['logo_size_width'] = $fileLogo ? $fileLogo[0] : 0;
            $default_info['logo_size_height'] = $fileLogo ? $fileLogo[1] : 0;

            //background
			if(isset($_POST['background_default'])){
				if($default_info['background'] != ''){//xoa file cu
					FileHandler::delete(SITEINFO_FOLDER.$default_info['background']);
				}
				$default_info['background'] = '';
			}else{
				$bg = $_FILES['background'];//favicon
				if($bg['name'] != ''){
					if(($bg['size'] > 0) && ($bg['size'] < CGlobal::$max_upload_size) && FileHandler::isImageFile($bg['name'])){
						$fname = FileHandler::getNameByTime($bg['name']);
						if(FileHandler::upload($bg['tmp_name'], SITEINFO_FOLDER.$fname)){
							if($default_info['background'] != ''){//xoa file cu
								FileHandler::delete(SITEINFO_FOLDER.$default_info['background']);
							}
							$default_info['background'] = $fname;
						}
					}
				}
			}

            //alert image
			if(isset($_POST['alert_default'])){
				if($default_info['alert'] != ''){//xoa file cu
					FileHandler::delete(SITEINFO_FOLDER.$default_info['alert']);
				}
				$default_info['alert'] = '';
			}else{
				$bg = $_FILES['alert'];//favicon
				if($bg['name'] != ''){
					if(($bg['size'] > 0) && ($bg['size'] < CGlobal::$max_upload_size) && FileHandler::isImageFile($bg['name'])){
						$fname = FileHandler::getNameByTime($bg['name']);
						if(FileHandler::upload($bg['tmp_name'], SITEINFO_FOLDER.$fname)){
							if($default_info['alert'] != ''){//xoa file cu
								FileHandler::delete(SITEINFO_FOLDER.$default_info['alert']);
							}
							$default_info['alert'] = $fname;
						}
					}
				}
			}

            //water mask
			 //logo
			if(isset($_POST['mask_default'])){
				$default_info['water_mark_img'] = '';
			}else{
				$mask = $_FILES['water_mark_img'];
				if($mask['name'] != ''){
					if(($mask['size'] > 0) && ($mask['size'] < CGlobal::$max_upload_size) && FileHandler::isImageFile($mask['name'])){
						$tail = FileHandler::getExtension($mask['name'], 'png');
						$fname = 'water_mask.'.$tail;
						if(FileHandler::upload($mask['tmp_name'], SITEINFO_FOLDER.$fname)){
							$default_info['water_mark_img'] = $fname;
						}
					}
				}
			}
		}

		if($this->errNum == 0){
			ConfigSite::saveModuleConfig();
			
            ConfigSite::setConfigToDB($this->key, serialize($default_info));

			//site keywords
            ConfigSite::setConfigToDB($this->keywords, Url::getParam('keywords', $this->siteConf['keywords']));

			//site description
            ConfigSite::setConfigToDB($this->description, Url::getParam('description', $this->siteConf['description']));

			//site black ips
            ConfigSite::setConfigToDB($this->black_ips, Url::getParam('black_ips', $this->siteConf['black_ips']));

			//image server
			ConfigSite::setConfigToDB($this->imgServer, serialize($imgServer));

			//xoa cache config
			ConfigSite::clearCacheConfig();
	
			Url::goback();
		}
    }  
}