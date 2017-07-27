<?php
class listUserForm extends Form{
	var $msg = '';
	private $cmd = 'user', $table = T_USERS, $table_role = T_USER_ROLES;
	public $perm, $link;
	function __construct(){
		parent::__construct();
		$action = Url::getParamAdmin('action','');
		if($action == 'delete'){
			$this->deleteUser();
		}elseif($action == 'clean'){
			$this->cleanUser();
		}

		$this->link_js_me('admin_user.js', __FILE__);
		
		$this->perm = array(
            'add' => User::user_access("add user"),
            'edit' => User::user_access("edit user"),
            'del' => User::user_access("delete user"),
			'block' => User::user_access("block user"),
			'log' => User::user_access("log user")
        );
		$this->link = array(
            'add' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'add')),
            'edit' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'edit')),
            'del' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'delete')),
			'clean' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'clean')),
			'log' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'log'))
        );
	}

	function draw(){
		global $display;
		$check_online_time = TIME_NOW - CGlobal::$checkOnlineTime;
		
		//search info
		$search_username = Url::getParam('search_username', '');
		$search_email= Url::getParam('search_email', '');
		$search_role = Url::getParamInt('role',0);
		$search_status = Url::getParamInt('status',1);
		
		//search time
		$time_from = Url::getParam('created_time','');
		$time_to = Url::getParam('created_time_to','');
		
		$curID = User::id();
		
		//sql condition
		$order= "ORDER BY created DESC";
		
		$cond = array();
		
		switch($search_status){
			case 0:
				$cond[] = 'status = 0'; break;
			case 9:
				$cond[] = 'status = 1 AND is_active = 0'; break;
			case 1:case 8:default:
				$cond[] = 'status = 1 AND is_active = 1';
				if($search_status == 8){
					$cond[] = 'last_action >= '.$check_online_time;
				}
				break;
		}
		
		if($search_username != ''){
			$cond[] = "username LIKE '%$search_username%'";
		}
		if($search_email != ''){
			$cond[] = "email LIKE '%$search_email%'";
		}
		if($search_role > 0){
			$strIDs = '';
			$res = DB::query("SELECT uid FROM ".$this->table_role." WHERE rid = ".$search_role);
			while($r = @mysql_fetch_assoc($res)){
				$strIDs .= $r['uid'].',';
			}
			if($strIDs != ''){
				$strIDs = substr($strIDs, 0, -1);
				$cond[] = "id IN ($strIDs)";
			}
		}
		//tim theo tinh thanh
		$province_user = User::getUserProvince(true);
		$pLength = count($province_user);
		if($pLength < count(CGlobal::$province_active)){
			$conArr = array();
			for($i=0;$i<$pLength;$i++){
				$conArr[$i] = "'{$province_user[$i]}'";
				for($j=$i+1;$j<$pLength;$j++){
					$conArr[$i.'-'.$j] = "'{$province_user[$i]},{$province_user[$j]}'";
				}
			}
			$where = "";
			foreach($conArr as $str){
				$where .= "province = $str OR ";
			}
			$cond[] = "($where)";
		}
		if($curID != 1){
			$cond[] = "(id != 1)";
		}
		
		// search time
		if($time_from){
			$date_arr = explode('-',$time_from);
			if(isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])){
				$created_time_from = mktime(0,0,0,(int)$date_arr[1],(int)$date_arr[0],(int)$date_arr[2]);
				$cond[] = "last_login >= $created_time_from";
			}
		}
		if($time_to){
			$date_arr = explode('-',$time_to);
			if(isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])){
				$created_time_to = mktime(23,59,59,(int)$date_arr[1],(int)$date_arr[0],(int)$date_arr[2]);
				$cond[] = "last_login <= $created_time_to";
			}
		}
		
		$data= array();
		$paging = '';
		$recperpage = 20;

		//get now page user
		$where = FunctionLib::addCondition($cond, true);
		$sql = "SELECT * FROM ".$this->table." $where $order";
		$res = Pagging::pager_query($sql,$recperpage);
		if($res){
			while($r = @mysql_fetch_assoc($res)){
				$data[$r['id']] = $this->parseItem($r, $curID, $check_online_time);
			}
			if(!empty($data)){
				$ids = array_keys($data);
				$ids = implode(',', $ids);
				$res = DB::query("SELECT * FROM ".$this->table_role." WHERE uid IN ($ids) ORDER BY uid");
				while($r = @mysql_fetch_assoc($res)){
					if(isset(CGlobal::$permission_group[$r['rid']])){
						$data[$r['uid']]['roles'] .= CGlobal::$permission_group[$r['rid']]['title'].', ';
					}
				}
				foreach($data as $k => $v){
					$data[$k]['roles'] = substr($v['roles'], 0, -2);
				}
			}
			$paging = Pagging::getPager(3, false, 'page_no', true);
		}

		if($this->msg != ''){
			$this->setFormSucces('', $this->msg);
		}

		User::userAutoList($this, array(
			'items' => $data,
			'log2step' => ConfigSite::getConfigFromDB('log2step', 0, false, 'site_configs'),
			'pagging' => array(
				'start_page' => (Pagging::$page-1)*$recperpage,
				'total_item' => Pagging::$totalResult,
				'total_page' => Pagging::$totalPage,
				'pager'	=> $paging
			)
		));
	}

	function on_submit(){
		if(!isset($_POST['search']) && User::user_access('delete user',0,'access_denied')){
			if(!empty($_POST['selected_ids'])){
				$delUserArr = array();
				foreach($_POST['selected_ids'] as $id){
					if($id != 1){
						if($id != User::id()){
							if(User::user_role_compare_byID($id)){
								$delUserArr[] = $id;
							}
							else {
								$this->setFormError('', "Bạn không thể xóa user ($id) có quyền lớn hơn bạn");
							}
						}else{
							$this->setFormError('', "Bạn không thể xóa account của mình");
						}
					}else{
						$this->setFormError('', "Bạn không thể xóa account Admin hệ thống");
					}
				}
				if(!empty($delUserArr)){
					$ids = implode(',', $delUserArr);
					DB::update($this->table,array("status" => 0),"id IN ($ids)");
					foreach($delUserArr as $id){
						User::getUser($id, false, true);
					}
					$this->setFormSucces('', "Xóa thành công! User có mã ($ids) đã bị xóa");
				}					
			}else{
				$this->setFormError('', "Bạn chưa chọn user cần xóa");
			}
		}
	}

	function deleteUser(){
		$id = Url::getParamInt('id', 0);
		if($id > 0){
			if($id != 1){
				if($id != User::id()){
					if(User::user_role_compare_byID($id)){
						DB::update($this->table, array("status" => 0), "id=$id");
						//delete cached
						$user = User::getUser($id, true);
						$this->setFormSucces('', 'Đã xóa user: <b>'.$user['username'].'</b>');
					}
					else {
						$this->setFormError('', "Bạn không thể xóa user có quyền lớn hơn bạn");
					}
				}else{
					$this->setFormError('', "Bạn không thể xóa account của mình");
				}
			}else{
				$this->setFormError('', "Bạn không thể xóa account Admin hệ thống");
			}
		}
	}
	
	function cleanUser(){
		$id = Url::getParamInt('id', 0);
		if($id > 0){
			$user = User::getUser($id, true);
			if(Url::getParamInt('kickout') == 1){
				User::kickout($id, true);
				$this->msg .= 'Đã buộc user: <b>'.$user['username'].'</b> thoát khỏi hệ thống';
			}else{
				$this->msg .= 'Đã xóa cache của user: <b>'.$user['username'].'</b>';
			}
		}
	}
	
	function optRole($default = 0){
		$sql = "SELECT id, title FROM ".T_ROLES." ORDER BY title, created DESC";
		$res = DB::query($sql);
		$p   = array("0" => " -- Chọn quyền -- ");
		while($r = mysql_fetch_assoc($res)){
			$p[$r['id']] = $r['title'];
		}
		return FunctionLib::getOption($p, $default);
	}
	
	function parseItem(&$r = array(), $curID, $check_online_time){
		if(!empty($r)){
			$r['roles'] = '';
			if($r['province'] == 0){
				$r['province'] = '<b>Toàn Quốc</b>';
			}else{
				$province = explode(',',$r['province']);
				$r['province'] = '';
				foreach($province as $key){
					if(isset(CGlobal::$province_active[$key])){
						$r['province'] .= CGlobal::$province_active[$key]['title'].', ';
					}
				}
				$r['province'] = substr($r['province'],0,-2);
			}
			$r['loginAs'] = $curID != $r['id'];
			$r['hide_delete'] = $curID == $r['id'];
			$r['online'] = ($r['last_action'] >= $check_online_time) ? 1 : 0; //neu ko co hoat dong trong 1 tieng thi coi la offline
			
			//information
			$r['information'] = '<b>U:</b> <b style="color:red" class="f14">'.$r['username'].'</b><br />';
			if($r['fullname']){
				$r['information'] .= '<b>N:</b> '.$r['fullname'].'<br />';
			}
			if($r['email']){
				$r['information'] .= '<b>E:</b> '.$r['email'].'<br />';
			}
			if($r['mobile_phone']){
				$r['information'] .= '<b>T:</b> '.$r['mobile_phone'].'<br />';
			}
			//qr code
			$r['qrCode'] = '';
			if($r['qrCodeUrl']){
				$r['qrCode'] .= '<div class="m-t-5"><img title="'.$r['secret'].'" width="80" src="'.$r['qrCodeUrl'].'" /></div>';
			}
			$r['qrCode'] .= '<div class="m-t-5"><a href="javascript:void(0)" onclick="shop.admin.user.sendQR('.$r['id'].')">Gửi mã</a></div>';
			if($r['ignoreQR'] == 0){
				$r['qrCode'] .= '<div class="m-t-5"><a href="javascript:void(0)" onclick="shop.admin.user.ignoreQR('.$r['id'].', 1)">Bỏ qua</a></div>';
			}else{
				$r['qrCode'] .= '<div class="m-t-5"><a href="javascript:void(0)" onclick="shop.admin.user.ignoreQR('.$r['id'].', 0)">Bắt buộc</a></div>';
			}
			//login tab
			$r['logintab'] = '';
			if($r['last_login']){
				$r['logintab'] .= '<p>'.FunctionLib::dateFormat($r['last_login'], 'd/m/Y H:i:s').'</p>';
			}
			if($r['loginAs']){
				$r['logintab'] .= '<div class="m-t-5"><a href="javascript:void(0)" onclick="shop.login_as('.$r['id'].')" class="tipS" original-title="Đăng nhập bằng account này">Login as</a></div>';
			}
			$r['online_icon'] = array(
				'icon' => ($r['online'] == 1) ? 'person' : 'person_outline',
				'des'  => ($r['online'] == 1) ? 'Online from '.FunctionLib::dateFormat($r['last_action'], 'H:i') : 'Offline',
				'color'=> ($r['online'] == 1) ? '' : 'grey'
			);
			$r['log'] = "javascript: shop.admin.showLog(".$r['id'].",'user')";
			if($r['id'] != 1 && !$r['hide_delete']){
				$r['active_icon'] = array(
					'icon' => 'check_circle',
					'des'  => "Click để thay đổi trạng thái kích hoạt",
					'color'=> ($r['is_active'] == 1) ? '' : 'grey',
					'link' => "javascript: shop.admin.user.changeActive(this,".$r['id'].",".$r['is_active'].")"
				);
			}else{
				$r['btn-del'] = array('hide' => true);
				$r['btn-del-check'] = array('hide' => true);
			}
			if($r['id'] != 1){
				$r['kick'] = $this->link['clean']."?id=".$r['id']."&kickout=1";
			}
			$r['cache'] = $this->link['clean']."?id=".$r['id'];
		}
		return $r;
	}
}
