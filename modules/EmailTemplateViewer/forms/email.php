<?php
class EmailTemplateViewerForm extends Form{
	function __construct(){
		parent::__construct();
	}
	
	function draw(){
		global $display;

		$email = Url::getParam('email_test', 'lymanhha@gmail.com');
		$tpl = Url::getParam('tpl', '');
		$lang = Url::getParam('lang', Language::$defaultLang);

		switch($tpl){
			case 'test'://Email dat hang thanh cong
				$tpl = 'mailTest';
			break;
		}
		$content = '';
		if(!empty($tpl)){
			$lang = ($lang == Language::$defaultLang) ? '' : $lang.'/';

			$display->add('WEB_ROOT', WEB_ROOT);
			$display->add('site_name', CGlobal::$site_name);
			$display->add('logo', EmailLib::$logo);
			$display->add("support_msg", CGlobal::$messenger_support[22]);
			$display->add("support_city", CGlobal::$province_active[22]);

            $content = $display->output($lang.$tpl,true);
		}
        $this->buildThemeTpl($content, $email);
	}
	
	function on_submit(){
		$email = Url::getParam('email_test');
		$tpl = Url::getParam('tpl', '');
        $lang = Url::getParam('lang', Language::$defaultLang);
        Language::cookie(true, $lang);

		if(!empty($tpl) && $tpl != 'footer_mail'){
			switch($tpl){
				case 'test':
					$data = $this->mail_data($tpl);
					$data['email'] = $email;
					if(!EmailLib::sendEmailTest($data)){
						$this->setFormError('','Không gửi được Email cho: <b>'.$email.'</b>');
					}else{
						$this->setFormSucces('','Đã gửi Email cho <b>'.$email.'</b>');
					}
					break;
			}
		}else{
			$this->setFormError('','Chưa chọn mẫu Email');
		}
	}

	function mail_data($tpl = ''){
		$data = array();
		switch($tpl){
            case 'test':
                $data['msg'] = 'Hello world';
                break;
		}
		return $data;
	}

    function buildThemeTpl($content= '', $defEmail = ''){
        $menuArr = array(
            array('title' => 'Email test',
                'link' => Url::buildAdminURL('admin', array('cmd' => 'email', 'tpl' => 'test'))),
        );
        $cur = REQUEST_SCHEME.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $cur = explode('&', $cur);
        $cur = $cur[0];

		//create form
		$this->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'POST'
		));
		
		//add group search
		$this->layout->addGroup('main', array('title' => 'Mẫu Email hiện tại'));
		$this->layout->addGroup('main2', array('title' => 'Ngôn ngữ'));
		$this->layout->addGroup('main3', array('title' => 'Email người nhận'));
		
		//add item to search
		$options = '<option value="'.Url::build('admin', array('cmd' => 'email')).'">-- Chọn --</option>';
        foreach($menuArr as $m){
            $options .= '<option value="'.$m['link'].'"'.($cur == $m['link']?' selected':'').'>'.$m['title'].'</option>';
        }
		$this->layout->addItem('tpl', array(
			'type'	=> 'select',
			'options' => $options,
			'ext' => array(
				'onchange' => 'if(this.value != \'0\') window.location.href = this.value'
			)
		), 'main');
		
		$curLang = Url::getParam('lang', Language::$defaultLang);
		$this->layout->addItem('lang', array(
			'type'	=> 'select',
			'options' => FunctionLib::getOption(Language::$listLangOptions, $curLang),
			'ext' => array(
				'onchange' => 'if(this.value != \'0\') window.location.href = \''.$cur.'&lang='.'\'+this.value'
			)
		), 'main2');

		$this->layout->addItem('email_test', array(
			'type'	=> 'text',
			'value' => $defEmail,
			'ext' => array(
				'onfocus' => 'this.select()'
			)
		), 'main3');
		
        $this->layout->genFormAuto($this, array(
			'html_search_label' => $this->layout->genLabelAuto(array('title' => 'BỘ LỌC', 'des' => 'Chọn ra mẫu email gửi thử nghiệm')),
			'html_search_button' => $this->layout->genButtonAuto(array(
				'title' => '&nbsp;Gửi Email',
				'icon'  => 'email',
				'style' => 0,
				'color' => 'green',
				'type'  => 0,
				'size'  => 1
			)),
			'html_view_label' => $this->layout->genLabelAuto(array('title' => 'MẪU SẼ GỬI')),
			'html_view_table' => $content
		));
    }
}
