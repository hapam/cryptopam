<?php
class EditProvinceForm extends Form{
	private $table = T_PROVINCE, $action = 'add';
	public $id, $province;
	function __construct(){
		parent::__construct();
		$this->action = Url::getParamAdmin('action');
		if($this->action == 'edit'){
			$this->id = Url::getParamInt('id', 0);
			if($this->id > 0){
				if($this->province = DB::select($this->table,'id='.$this->id)){
					$this->province = $this->province[$this->id];
				}
			}
			if(!$this->province){
				Url::redirect('admin', array('cmd' => 'province'));
			}
		}
		$this->link_js_me('admin_province.js', __FILE__);
	}

	function draw(){
		global $display;

		$yahooNum  		=	Url::getParamInt('list_item_yahoo_num', 1);
        $skypeNum  		=	Url::getParamInt('list_item_skype_num', 1);
    	$yahooGet = array();
		if($yahooNum > 0){
			for ($i = 1; $i <= $yahooNum; $i++) {
				$id = Url::getParam('yahoo'.$i, '');
				if($id){
					$yahooGet[$i]['id'] = $id;
					$yahooGet[$i]['name'] = Url::getParam('yahooName'.$i, '');
				}
			}
		}
    	$skypeGet = array();
		if($skypeNum > 0){
			for ($i = 1; $i <= $skypeNum; $i++) {
				$id = Url::getParam('skype'.$i, '');
				if($id){
					$skypeGet[$id]['id'] = $id;
					$skypeGet[$id]['name'] = Url::getParam('skypeName'.$i, '');
				}
			}
		}
		
		$yahooP = ($this->province['yahoo'] != '') ? unserialize($this->province['yahoo']) : '' ;
		$yahoo = array();
		if(!empty($yahooP)){
			$i = 1;
			foreach ($yahooP as $k=>$y){
				$yahoo[$i]['id'] = $k;
				$yahoo[$i]['name'] = $y;
				$i++;
			}
		}
    	$skypeP = ($this->province['skype'] != '') ? unserialize($this->province['skype']) : '' ;
		$skype = array();
		if(!empty($skypeP)){
			$i = 1;
			foreach ($skypeP as $k=>$y){
				$skype[$i]['id'] = $k;
				$skype[$i]['name'] = $y;
				$i++;
			}
		}
		$yahooNum = ($yahooGet) ? count($yahooGet) : count($yahoo);
		$skypeNum = ($skypeGet) ? count($skypeGet) : count($skype);
		$skypeNum = ($skypeNum == 0) ? 1 : $skypeNum;
		$yahooNum = ($yahooNum == 0) ? 1 : $yahooNum;
		
		
        $display->add('yahooNum',$yahooNum);
        $display->add('skypeNum',$skypeNum);
        $display->add('yahoo',($yahooGet) ? $yahooGet : $yahoo);
        $display->add('skype',($skypeGet) ? $skypeGet : $skype);

		$data = array(
			'cskh_html' => $display->output('edit', true)
		);
		Province::provinceAutoEdit($this, $data, 'draw');
    }
	
	function on_submit(){
		//kiem tra xem da ton tai hay chua
		$title = Url::getParam('title');
		if($title != $this->province['title']){
			$res = DB::fetch("SELECT * FROM ".T_PROVINCE." WHERE title = '$title' LIMIT 0,1");
			if($res){
				$this->setFormError('title', 'Vùng miền này đã tồn tại');
			}
		}

		if($this->errNum <= 0){
			$data = array();
			if(Province::provinceAutoEdit($this, $data, 'submit')){
				$yahooNum  		=	Url::getParamInt('list_item_yahoo_num', 0);
				$skypeNum  		=	Url::getParamInt('list_item_skype_num', 0);
				$yahoo = array();
				if($yahooNum > 0){
					for ($i = 1; $i <= $yahooNum; $i++) {
						$id = Url::getParam('yahoo'.$i, '');
						if($id){
							$yahoo[$id] = Url::getParam('yahooName'.$i, '');
						}
					}
				}
				$skype = array();
				if($skypeNum > 0){
					for ($i = 1; $i <= $skypeNum; $i++) {
						$id = Url::getParam('skype'.$i, '');
						if($id){
							$skype[$id] = Url::getParam('skypeName'.$i, '');
						}
					}
				}
				$data['skype'] = serialize($skype);
				$data['yahoo'] = serialize($yahoo);
				
				if($this->action == 'edit'){
					DB::update($this->table, $data,'id='.$this->id);
				}
				else{
					DB::insert($this->table, $data);
				}
				$_SESSION['province_update_cached'] = true;
				Url::redirect('admin', array('cmd' => 'province'));
			}
		}
		$this->setFormError('', 'Lỗi! Không lưu được tỉnh thành');
    }
}