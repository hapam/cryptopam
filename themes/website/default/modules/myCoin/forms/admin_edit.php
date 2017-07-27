<?php
class EditmyCoinForm extends Form {
    private $cmd = 'crypto', $action = 'add', $table = T_COIN;
	public $item, $id;
    function __construct(){
		parent::__construct();
        $this->id = Url::getParamInt('id', 0);
        $this->action = Url::getParamAdmin('action', $this->action);
        if ($this->id > 0) {
            $this->item = DB::fetch("SELECT * FROM {$this->table} WHERE id={$this->id}");
            if (!$this->item) {
                Url::redirect('admin', array('cmd' => $this->cmd));
            }
        }
		if($this->action == 'delete'){
			$this->delete();
		}
	}

    function draw() {
		$data = array();
        Crypto::autoEdit($this, $data, 'draw');
    }

    function on_submit() {
		$data = array();
		if(Crypto::autoEdit($this, $data, 'submit')){
			//upload image
			$err = '';
			if ($this->errNum == 0){
				if($this->action == 'edit'){
					DB::update($this->table, $data,'id='.$this->id);
				}
				else{
					DB::insert($this->table, $data);
				}
				//chuyen ve trang quan tri
				Url::redirect('admin', array('cmd' => $this->cmd));
			}
		}
		$this->setFormError('', 'Lỗi! Không lưu được dữ liệu');
    }

	function delete(){
		DB::update($this->table, array('status' => -1), "id=".$this->id);
		Url::redirect('admin', array('cmd' => $this->cmd));
	}
}