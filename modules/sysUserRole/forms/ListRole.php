<?php
class ListRoleForm extends Form{
	private $cmd = 'user-role', $msg = '', $notDelete, $table = T_USER_ROLES, $table_role = T_ROLES;
	public $perm, $link;
	function __construct(){
		parent::__construct();

		$action = Url::getParamAdmin('action','');
		if($action == 'delete')	{
			$this->deleteRole();
		}elseif($action == 'clean'){
			$this->cleanRole();
		}
		
		$this->perm = array(
            'add' => User::user_access("add role"),
            'edit' => User::user_access("edit role"),
            'del' => User::user_access("delete role"),
			'per' => User::user_access("permission")
        );
		$this->link = array(
            'add' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'add')),
            'edit' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'edit')),
            'del' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'delete')),
			'clean' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'clean')),
			'per' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'permission'))
        );
		$this->notDelete = array(1, 2, 3, 4);
	}

	function draw(){
		$data = CGlobal::$permission_group;
		$curRank = !User::is_big_boss() ? User::user_rank() : -1000;
		foreach($data as $k => $r){
			if($curRank > $r['rank']){
				unset($data[$k]);
			}else{
				$data[$k]['created'] = FunctionLib::dateFormat($data[$k]['created'], "d-m-Y");
				$data[$k]['permit'] = ($r['id'] == 1) ? '<p align="center"><b>-- FULL ACCESS --</b></p>': str_replace(',',', ',$data[$k]['permit']);
				if($r['id'] != 1){
					$data[$k]['perm'] = $this->link['per'].'?id='.$r['id'];
					$data[$k]['cache'] = $this->link['clean'].'?id='.$r['id'];
					if(in_array($r['id'], $this->notDelete)){
						$data[$k]['btn-del']['hide'] = true;
						$data[$k]['btn-del-check']['hide'] = true;
					}
				}else{
					$data[$k]['btn-del']['hide'] = true;
					$data[$k]['btn-edit']['hide'] = true;
					$data[$k]['btn-del-check']['hide'] = true;
				}
			}
		}
		if($this->msg != ''){
			$this->setFormSucces('', $this->msg);
		}
		UserRole::autoList($this, array(
			'items' => $data
		));
	}

	function on_submit(){
		if(User::user_access('delete role',0,'access_denied')){
			if(!empty($_POST['selected_ids'])){
				$delRoleArr = array();
				foreach($_POST['selected_ids'] as $id){
					if(!in_array($id, $this->notDelete)){
						$delRoleArr[] = $id;
					}
				}
				if(!empty($delRoleArr)){
					$ids = implode(',', $delRoleArr);
					DB::delete($this->table_role,"id IN ($ids)");
					CacheLib::delete('user-roles', 'roles/');
					$this->setFormSucces('', "Xóa thành công");
					Url::redirect('admin', array('cmd' => $this->cmd));
				}
			}else{
				$this->setFormError('', "Bạn chưa chọn Role cần xóa");
			}
		}else{
			$this->setFormError('', "Bạn không có quyền xóa Role");
		}
	}
	
	function cleanRole(){
		$id = Url::getParamInt('id', 0);
		if($id > 0){
			$res = DB::query("SELECT uid FROM ".$this->table." WHERE rid = $id");
			while($u = @mysql_fetch_assoc($res)){
				if(User::getUser($u['uid'], false, true)){
					$this->msg .= '<div class="m-t-5>Đã xóa cache cho user '.$u['uid'].'</div>';
				}
			}
		}
	}

	function deleteRole(){
		$id = Url::getParamInt('id', 0);
		if($id > 0 && !in_array($id, $this->notDelete) && User::user_role_compare(array($id => $id))){
			DB::delete($this->table_role,"id=$id");
			CacheLib::delete('user-roles', 'roles/');
			Url::redirect('admin', array('cmd' => $this->cmd));
		}
		$this->setFormError('', 'Không thể xóa! ID <b>'.implode(', ', $this->notDelete).'</b> là nhóm quyền cố định');
	}
}
