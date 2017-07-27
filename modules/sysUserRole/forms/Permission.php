<?php
class PermissionForm extends Form{
	var $id, $item, $cmd = 'user-role';
	function __construct(){
		parent::__construct();
		
		$this->id = Url::getParamInt('id', 0);
		if($this->id > 0){
			$this->item = CGlobal::$permission_group[$this->id];
		}
	}

	function draw(){
		$data = array(
			'items' => $this->item,
			'permission' => $this->makeGroupPerm(),
			'access' => isset(User::$current->data['role_ids'][$this->id]) || User::user_role_compare(array($this->id => $this->id))
		);
		UserRole::autoPermission($this, $data, 'draw');
	}
	
	function makeGroupPerm(){
		$perList = array(
			'System' => array(),
			'Admin' => array()
		);
		foreach(CGlobal::$permission as $name => $perm){
			if(isset(CGlobal::$coreModules[$name])){
				$perList['System'][$name] = $perm;
			}else{
				$perList['Admin'][$name] = $perm;
			}
		}
		return $perList;
	}

	function on_submit(){
		unset($_POST['form_block_id']);
		unset($_POST['id']);
		unset($_POST['__myToken']);

		if(!empty($_POST) && $this->id > 0){
			$key = array_keys($_POST);
			$key = implode(',', $key);
			DB::update(T_ROLES, array('permit' => $key), "id=".$this->id);
			CacheLib::delete('user-roles', 'roles/');
		}
		Url::redirect('admin',array('cmd'=>$this->cmd));
	}
}
