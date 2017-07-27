<?php
class EditRoleForm extends Form{
	private $cmd = 'user-role', $table = T_ROLES;
	public $item, $action, $id;
	function __construct(){
		parent::__construct();
		$this->id  = Url::getParamInt('id', 0);
		$this->action = Url::getParamAdmin('action', 'add');
		if($this->action == 'edit'){
			if($this->id == 1){
				exit("<h1 align='center'>FAIL!!! Can not modify ROOT <br /><a href='javascript:history.go(-1)'>Go back</a></h1>");
			}
			if(isset(CGlobal::$permission_group[$this->id])){
				$this->item = CGlobal::$permission_group[$this->id];
			}else{
				Url::redirect('admin', array('cmd' => $this->cmd));
			}
		}
	}
	
	function draw(){
		$access = ($this->id > 0) ? (isset(User::$current->data['role_ids'][$this->id]) || User::user_role_compare(array($this->id => $this->id))) : true;
		$data = array('access' => $access);
		UserRole::autoEdit($this, $data, 'draw');
	}
	
	function on_submit(){
		$data = array();
		if(UserRole::autoEdit($this, $data, 'submit')){
			if($this->id == 0 || isset(User::$current->data['role_ids'][$this->id]) || User::user_role_compare(array($this->id => $this->id))){
				$curRank = User::user_rank();
				//valid rank
				$check = $curRank < $data['rank'];
				$msg = 'lớn hơn '.$curRank;

				//if set rank for current role
				if(isset(User::$current->data['role_ids'][$this->id])){
					$check = $curRank <= $data['rank'];
					$msg = 'lớn hơn hoặc bằng '.$curRank;

					$max = false;
					$max_point = false;
					foreach(CGlobal::$permission_group as $rid => $v){
						if($max_point){
							$max = $v['rank'];
							break;
						}
						if(isset(User::$current->data['role_ids'][$rid])){
							$max_point = true;
						}
					}
					if($max != false){
						$check = $check && ($data['rank'] < $max);
						$msg .= ' và nhỏ hơn '.$max;
					}
				}
				if($check){
					$exist = DB::exists("SELECT id FROM ". $this->table. " WHERE title = '".$data['title']."' AND id != ".$this->id);
					if(!$exist) {
						if($this->action == 'add'){
							$data['created'] = time();
							$this->id = DB::insert($this->table, $data);
						}else{
							DB::update($this->table, $data, "id=".$this->id);
						}
						CacheLib::delete('user-roles', 'roles/');
						Url::redirect('admin',array('cmd'=> $this->cmd));
					}else{
						$this->setFormError('', "Tên quyền này đã tồn tại");
					}
				}else{
					$this->setFormError('', 'Xếp hạng phải '.$msg);
				}
			}else{
				$this->setFormError('', 'Không có quyền thực hiện thao tác với nhóm quyền cao hơn');
			}
		}
	}
}
