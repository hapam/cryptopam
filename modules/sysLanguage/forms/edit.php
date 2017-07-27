<?php
class EditLangForm extends Form{
	private $cmd = 'lang', $action = 'add', $table = T_LANG;
	public $item, $id = 0;
	
	function __construct(){
		parent::__construct();

		$this->action = Url::getParamAdmin('action', $this->action);
		if($this->action == 'edit'){
			$this->id = Url::getParamInt('id',0);
			if($this->id > 0){
				$this->item = DB::fetch("SELECT * FROM {$this->table} WHERE is_main = 1 AND id = {$this->id}");
			}
			if(empty($this->item)){
				Url::redirect('admin', array('cmd' => $this->cmd));
			}
		}
	}

    function draw(){
		$data = array();
		Language::autoEdit($this, $data, 'draw');
    }
	
	function on_submit(){
		$data = array();
		if(Language::autoEdit($this, $data, 'submit')) {
			if(empty(Language::$arrLang)){
				Language::loadWordsFromLang(Language::$activeLang); // khoi tao de check neu chua co
			}
			$title = $data['title'];
            $str_check = strtolower(StringLib::stripUnicode($title));
			if (!isset(Language::$arrLang[$str_check])) {
                if ($this->action == 'edit') {
                    if ($this->item['type'] == 1) {
                        DB::update($this->table, array('title' => $title), "id=" . $this->id);
                    } else {
                        $this->setFormError('title', 'Từ gốc lấy từ hệ thống không được phép sửa');
                    }
                } else {
                    Language::addWordsToDB($title, Language::$defaultLang, 1);
                }
            }else {
                $this->setFormError('title', 'Từ gốc này đã tồn tại. Vui lòng nhập từ khác.');
            }
        }
        if($this->errNum == 0) {
            Url::redirect('admin', array('cmd' => $this->cmd));
		}
    }
}