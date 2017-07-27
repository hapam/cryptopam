<?php
class logCenter{
	static $logAction = array(
		'user' => array(
			'pass' 	=> 'Đổi mật khẩu',
			'login'	=> 'Đăng nhập',
			'logout'=> 'Thoát',
			'login_as' => 'Đăng nhập vào tài khoản quản trị',
			'active' => 'Mở khóa tài khoản quản trị',
			'unactive' => 'Khóa tài khoản quản trị',
			'del' => 'Xóa tài khoản quản trị',
			'perm' => 'Thay đổi quyền tài khoản quản trị',
			'edit' => 'Thay đổi thông tin cá nhân tài khoản quản trị'
		)
	);
	
	static function adminGroupLog(){
		return array('user');
	}
	static function isAdminLog($type = ''){
		return in_array($type, self::adminGroupLog());
	}
	static function isActive(){
		$log = ConfigSite::getConfigFromDB('save_log', 0, false, 'site_configs');
		return $log == 1;
	}

	static function set($type = '', $action = array(), $obj_id = 0, $fucked_id = 0, $note = '', $system = false){
		if(self::isActive() && isset(logCenter::$logAction[$type]) && !empty($action)){
			$uid = 0;
			$uname = '';
			if($system){
				$uname = 'system';
				$uid = 0;
			}else{
				if(self::isAdminLog($type)){ //log do admin thuc hien
					$uid = User::id();
					$uname = User::username();
				}else{
					//log do khach hang thuc hien
					$uid = Customer::id();
					if($uid > 0){
						$uname = Customer::$current->data['email'];
					}else{
						$uname = 'Guest';
					}
				}
			}
			if($uname != ''){
				$time = time();
				$ip = FunctionLib::ip();
				$note = addslashes($note);
				if(is_array($action)){
					$insert = array();
					foreach($action as $a){
						if(isset(logCenter::$logAction[$type][$a])){
							$insert[] = "($uid,'$uname',$obj_id,$fucked_id,'$a','$note',$time,'$type','$ip')";
						}
					}
					if(!empty($insert)){
						DB::query("INSERT ".T_LOGS." (`uid`,`uname`,`object_id`,`fucked_id`,`action`,`note`,`time`,`type`,`ip`) VALUES ".implode(',', $insert));
					}
				}elseif(isset(logCenter::$logAction[$type][$action])){
					DB::insert(T_LOGS, array(
						'uid' => $uid,
						'uname' => $uname,
						'object_id' => $obj_id,
						'fucked_id' => $fucked_id,
						'action' => $action,
						'note' => $note,
						'time' => $time,
						'type' => $type,
						'ip' => $ip
					));
				}
			}
		}
		return false;
	}
	
	static function get($type = '', $data = array(), $from = 0, $to = 50, &$total = 0){
		$result = array();
		if(isset(logCenter::$logAction[$type])){
			$condition = array();
			if(!empty($data)){
				if(isset($data['type']) && !empty($data['type'])){
					if(is_array($data['type'])){
						$condition[] = "type IN ('".implode("','",$data['type'])."')";
					}else{
						$condition[] = "type = '{$data['type']}'";
					}
				}
				if(isset($data['uid']) && $data['uid'] > 0){
					$condition[] = "uid = {$data['uid']}";
				}
				if(isset($data['uname']) && $data['uname'] != ''){
					$condition[] = "uname = '{$data['uname']}'";
				}
				if(isset($data['object_id']) && $data['object_id'] > 0){
					$condition[] = "object_id = {$data['object_id']}";
				}
				if(isset($data['fucked_id']) && $data['fucked_id'] > 0){
					$condition[] = "fucked_id = {$data['fucked_id']}";
				}
				if(isset($data['action']) && isset(logCenter::$logAction[$type][$data['action']])){
					$condition[] = "action = '{$data['action']}'";
				}
				if(isset($data['timeFrom']) && $data['timeFrom'] > 0){
					$condition[] = "time > {$data['timeFrom']}";
				}else{
					$data['timeFrom'] = 0;
				}
				if(isset($data['timeTo']) && $data['timeTo'] > $data['timeFrom']){
					$condition[] = "time < {$data['timeTo']}";
				}
			}
			$search_value = FunctionLib::addCondition($condition);
			$search_value = ($search_value != '') ? ' WHERE '.$search_value : '';
			
			$sql = "SELECT * FROM ".T_LOGS.$search_value." ORDER BY time DESC";
			
			//tinh tong so luong ban ghi
			$count = "SELECT count(*) as total ".substr($sql, stripos($sql,'from'), strlen($sql));
			$count = DB::query($count);
			if($count = @mysql_fetch_assoc($count)){
				$total = $count['total'];
			}
			//lay du lieu
			if($to > 0){
				if($from > $to){
					$from = 0;
				}
				$sql .= " LIMIT $from,".($to-$from);
			}
			$result = DB::fetch_all($sql);
		}
		return $result;
	}
	
	static function fetchLog($id = 0, $type = '', $from = 0, $to = 10){
		if($id > 0){
			$per = true;
			$existed = true;
			$show_note = false;
			$queryLog = array('type' => $type);
			switch($type){
				case 'user':
					$per = User::user_access('log user');
					$queryLog['uid'] = $id;
					$queryLog['type'] = self::adminGroupLog();
					foreach(logCenter::$logAction as $k => $logItem){
						if($k != 'user' && in_array($k,$queryLog['type'])){
							foreach($logItem as $key => $item){
								logCenter::$logAction['user'][$key] = $item;
							}
						}
					}
					$queryLog['uid'] = $id;
					$show_note = true;
					break;
			}
			if($existed){
				if($per){
					$users = array();
					$total = 0;
					$data = logCenter::get($type, $queryLog, $from, $to, $total);
					if(!empty($data)){
						$newData = array();
						foreach($data as $k => $v){
							$get_u = $v['fucked_id'] > 0 && (in_array($v['type'], array('user')));
							$v['time'] = array(
								FunctionLib::dateFormat($v['time'], 'd/m/Y'),
								FunctionLib::dateFormat($v['time'], 'H:i:s'),
							);
							
							$newData[] = array(
								'n' => $v['uname'],
								't' => $v['time'],
								'c' => logCenter::$logAction[$v['type']][$v['action']],
								'f' => $v['fucked_id'],
								'ip'=> $v['ip'],
								'cu'=> $get_u,
								'no' => $v['note']
							);
							if($get_u){
								$users[$v['fucked_id']] = $v['fucked_id'];
							}
						}
						$data = $newData;
						if(!empty($users)){
							$users = DB::fetch_all("SELECT id, username FROM ".T_USERS." WHERE id IN (".implode(',', $users).")");
						}
						return array(
							'data' => $data,
							'users' => $users,
							'total'=> $total,
							'show_note' => $show_note
						);
					}
				}else{
					return -1;
				}
			}
		}
		return 0;
	}
}
