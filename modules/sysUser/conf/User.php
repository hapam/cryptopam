<?php
User::$current = new User();
class User{
	var $data = array('id' => 0, 'email' => '', 'username' => 'guest', 'role_id' => 4, 'avatar' => '', 'role_ids' => array(), 'perm' => '', 'province' => 22);
	static $current=false;
	function __construct(){
		if(!isset($_SESSION[MEMCACHE_ID.'user_id'])){
			$_SESSION[MEMCACHE_ID.'user_id'] = 0;
		}
		if($_SESSION[MEMCACHE_ID.'user_id'] > 0){
			//check kick out
			self::kickout($_SESSION[MEMCACHE_ID.'user_id']);
			
			//lay thong tin
			$user = self::getUser($_SESSION[MEMCACHE_ID.'user_id']);
			if($user && (!USER_ACTIVE_ON || (USER_ACTIVE_ON && $user['is_active']==1))){
				//cap nhat thoi gian hoat dong
				DB::update(T_USERS, array('last_action' => TIME_NOW), "id=".$user['id']);

				//check LOGIN AS mode
				$username = StringLib::trimSpace(CookieLib::get_cookie('loginAs', ''));
				if($username != ''){
					$userIsLoged = DB::fetch("SELECT * FROM ".T_USERS." WHERE username = '$username'");
					if($userIsLoged){
						$userIsLoged['role_ids'] = array();
						$res = DB::query("SELECT rid FROM ".T_USER_ROLES." WHERE uid = ".$userIsLoged['id']);
						while($row = @mysql_fetch_assoc($res)){
							$userIsLoged['role_ids'][$row['rid']] = $row['rid'];
						}
						$userIsLoged = self::get_user_roles($userIsLoged);
						CookieLib::my_setcookie('loginUser', $user['username']);
						$user = $userIsLoged;
					}
				}
				$this->data = $user;
				$_SESSION[MEMCACHE_ID.'username'] = $user['username'];
			}
			else{
				$last_action = 0;
				self::LogOut();
			}
		}
	}

	static function getUser($user_id = 0, $update_cache = false, $delcache = false){
		$user = array();
		if($user_id > 0){
			$is_id = preg_match("#^[0-9]*$#", $user_id); 
			$condition = !$is_id ? "username='$user_id'" : "id=$user_id";
			$cacheKey = "user:$user_id";
			$subDir = 'user';
			if($delcache){//Xoá cache
				CacheLib::delete($cacheKey, $subDir);
				return true;
			}
			if(!$update_cache){
				$user = CacheLib::get($cacheKey,0,$subDir);
			}
			if(empty($user)){
				$user = DB::fetch("SELECT * FROM ".T_USERS." WHERE $condition LIMIT 0,1");
				if(!empty($user)){
					$user['role_ids'] = array();
					$res = DB::query("SELECT rid FROM ".T_USER_ROLES." WHERE uid = ".$user['id']);
					while($row = @mysql_fetch_assoc($res)){
						$user['role_ids'][$row['rid']] = $row['rid'];
					}
					$user = self::get_user_roles($user);
					CacheLib::set($cacheKey, $user, 0, $subDir);
				}
			}
		}
		if(!empty($user)){
			$user['history'] = isset($_SESSION[MEMCACHE_ID.'user_history']) ? $_SESSION[MEMCACHE_ID.'user_history'] : array();
		}
		return $user;
	}

	static function LogIn($user_or_id = 0, $update = false){
		$user_id = $user_or_id ;
		if(is_array($user_or_id) && isset($user_or_id['id'])){
			$user_id = (int)$user_or_id['id'];
		}
		$_SESSION[MEMCACHE_ID.'user_id'] = $user_id;
		if($user_id > 0){
			$_SESSION[MEMCACHE_ID.'user_history'] = DB::fetch("SELECT last_ip, last_login FROM ".T_USERS." WHERE id=$user_id");
			DB::query("UPDATE ".T_USERS." SET last_ip='".FunctionLib::ip()."', last_login = '".TIME_NOW."' WHERE id=".$user_id);
			$user = self::getUser($user_id, $update);
			if($user){
				self::$current->data = $user;
				$_SESSION[MEMCACHE_ID.'username'] = $user['username'];
				CookieLib::my_setcookie(md5('uid'),FunctionLib::hiddenID($user['id']));
				//ghi lai log dang nhap
				logCenter::set('user', 'login');
			}
		}
	}

	static function LogOut($is_kickout = false){
		$uid = (int)$_SESSION[MEMCACHE_ID.'user_id'];
		if($uid > 0){
			//ghi lai log
			if($is_kickout){
				logCenter::set('user', 'logout', 0, 0, 'Admin kick out');
			}else{
				logCenter::set('user', 'logout');
			}

			//cap nhat lai thong tin truoc khi thoat
			DB::query("UPDATE ".T_USERS." SET last_ip='".FunctionLib::ip()."', last_action=0 WHERE id=$uid");

			$_SESSION[MEMCACHE_ID.'user_id'] = 0;
			$_SESSION[MEMCACHE_ID.'username'] = '';
			unset($_SESSION[MEMCACHE_ID.'user_history']);
	
			//Remove remember password cookies
			CookieLib::my_setcookie(md5("uid"),"",TIME_NOW-3600);
			CookieLib::my_setcookie(md5("password"),"",TIME_NOW-3600);
		}
	}

	static function is_block(){
		return isset(self::$current->data['block_id']) && (self::$current->data['block_id'] > 0);
	}
	
	static function is_login(){
		return (isset($_SESSION[MEMCACHE_ID.'user_id']) && $_SESSION[MEMCACHE_ID.'user_id'] > 0);
	}

	static function check_auto_login($user_id = 0, $password = ''){
		if($user_id > 0 && $password && !self::is_login()){
			$user_data = DB::fetch('SELECT * FROM '.T_USERS.' WHERE is_active = 1 AND status = 1 AND id='.$user_id,false,false);
			if($user_data && $user_data['password'] == $password){
				if(CGlobal::$changePassTime > 0 && (($user_data['last_changepass']+CGlobal::$changePassTime*24*3600) < TIME_NOW)){
					//ko cho dang nhap tu dong ma bat nhap mat khau moi
				}else if(CGlobal::$reLoginTime > 0 && (($user_data['last_login']+CGlobal::$reLoginTime*24*3600) < TIME_NOW)){
					//ko cho dang nhap tu dong ma bat dang nhap lai
				}else{
					//kiem tra OTP xem co chua
					$user_data['ignoreQR'];
					$log2step = ConfigSite::getConfigFromDB('log2step', 0, false, 'site_configs');
					$need2Step= false;
					if($log2step == 1){
						$need2Step = !Authentication::checkValid2Step($user_id);
					}
					if($user_data['ignoreQR'] == 1 || !$need2Step){
						User::Login($user_data);
						Url::redirect_current();
					}else{
						CookieLib::my_setcookie(md5("id_user_forstep2"), FunctionLib::hiddenID($user_data['id']), 86400 + TIME_NOW);
					}
				}
			}
		}
		self::LogOut();
	}

	static function encode_password($password = ''){
		return md5($password.USER_PASWORD_KEY);
	}

	static function is_username($str = ''){
		$str = trim($str);
		if($str != ''){
			$str_len = strlen($str);
			if($str_len >=3 && $str_len <=50){
				return (bool)preg_match("#^[_a-zA-Z][0-9_a-zA-Z]*$#", $str);
			}
		}
		return false;
	}

	static function is_big_boss(){
		return self::$current->data['id'] == 1;
	}

	static function is_root(){
		if(!empty(self::$current->data['role_ids'])){
			return array_key_exists(1, self::$current->data['role_ids']);
		}
		return false;
	}

	static function is_admin(){
		return self::user_access('access admin page');
	}

	static function get_user_roles($user = array(), $id = 0){
		if($id > 0){
			$user = self::getUser($id);
		}
		$user['perm'] = '';
		if(!empty($user['role_ids']) && is_array($user['role_ids'])){
			foreach($user['role_ids'] as $role){
				if(isset(CGlobal::$permission_group[$role])){
					$user['perm'] .= CGlobal::$permission_group[$role]['permit'].',';
				}
			}
			if($user['perm'] != ''){
				$user['perm'] = substr($user['perm'],0,-1);
			}
		}
		return $user;
	}
	
	static function user_access($per = '', $role = 0, $redirect = ''){
		if(self::is_big_boss() || self::is_root()){
			return true;
		}
		if($role > 0){
			$per_role  = isset(CGlobal::$permission_group[$role]) ? CGlobal::$permission_group[$role] : ' ';
		}else{
			$per_role  = array('permit' => self::$current->data['perm']);
		}
		$per = str_replace(' ','_',$per);
		if(stripos($per_role['permit'].',', $per.',') !== false){
			return true;
		}
		if($redirect == ''){
			return false;
		}
		if($redirect == 'access_denied'){
			Url::access_denied();
		}
		Url::redirect($redirect);
	}
	
	static function user_rank($role = array()){
		$curRank = 1000;
		$role = !empty($role) ? $role : self::$current->data['role_ids'];
		foreach($role as $rid){
			if(isset(CGlobal::$permission_group[$rid]) && CGlobal::$permission_group[$rid]['rank'] < $curRank){
				$curRank = CGlobal::$permission_group[$rid]['rank'];
			}
		}
		return $curRank;
	}

	static function user_role_compare($role = array()){
		//neu hien tai la ROOT hoac quyen so sanh rong thi oke luon
		if(self::is_big_boss() || empty($role)){
			return true;
		}
		//Neu quyen hien tai khong rong
		if(!empty(self::$current->data['role_ids'])){
			$curRank = self::user_rank();
			$comRank = self::user_rank($role);
			return $curRank < $comRank;
		}
		return false;
	}

	static function user_role_compare_byID($uid = 0){
		if($uid > 0){
			if(self::$current->data['id'] == $uid){
				return true;
			}
			$user = self::getUser($uid);
			if(isset($user['id']) && $user['id'] > 0){
				return self::user_role_compare($user['role_ids']);
			}
		}
		return false;
	}
	
	static function user_access_province($province_id = 0){
		if(self::is_root() || self::is_big_boss()){
			return true;
		}
		$province = self::getUserProvince(true);
		return in_array($province_id, $province);
	}
	
	static function getUserProvince($array = false){
		if(self::is_root() || self::is_big_boss()){
			$p = array_keys(CGlobal::$province_active);
			if(!$array){
				$p = implode(',', $p);
			}
			return $p;
		}
		if($array){
			return explode(',', self::$current->data['province']);
		}
		return self::$current->data['province'];
	}
	
	static function isToanQuoc($oCity = array()){
		if(!empty($oCity)){
			foreach(CGlobal::$province_active as $k => $v){
				if(!in_array($k, $oCity)){
					return false;
				}
			}
			return true;
		}
		return false;
	}

	static function username(){
		if(isset($_SESSION[MEMCACHE_ID.'user_id'])){
			if(isset($_SESSION[MEMCACHE_ID.'username'])&&$_SESSION[MEMCACHE_ID.'username']){
				return $_SESSION[MEMCACHE_ID.'username'];
			}
			elseif(isset(self::$current->data['username'])){
				return self::$current->data['username'];
			}
		}
		return '';
	}
	static function id(){
		return isset($_SESSION[MEMCACHE_ID.'user_id'])?(int)$_SESSION[MEMCACHE_ID.'user_id']:0;
	}
	
	//set = false: kickout check | true: setup kickout
	static function kickout($id = 0, $set = false){
		if($id > 0){
			$cacheKey = "user_kickout:$id";
			$subDir = 'user';
			if($set){
				DB::update(T_USERS, array("last_action" => (TIME_NOW - CGlobal::$checkOnlineTime - 69)), "id=$id");
				CacheLib::set($cacheKey, TIME_NOW, 0, $subDir);
			}else{
				$check = CacheLib::get($cacheKey, 0, $subDir);
				if($check && $check > 0){
					CacheLib::delete($cacheKey, $subDir);
					self::LogOut(true);
					Url::redirect_current();
				}
			}
		}
	}
	
	static function userAutoList(&$form, $data = array()){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET'
		));

		//add group search
		$form->layout->addGroup('main', array('title' => 'Tài khoản'));
		$form->layout->addGroup('filter', array('title' => 'Bộ lọc'));
		$form->layout->addGroup('login-time', array('title' => 'Thời gian đăng nhập'));
		
		//add item to search
		$form->layout->addItem('search_username', array(
			'type'	=> 'text',
			'title' => 'Tìm theo tên đăng nhập'
		), 'main');
		$form->layout->addItem('search_email', array(
			'type'	=> 'text',
			'title' => 'Email đăng kí'
		), 'main');
		//filter
		$form->layout->addItem('role', array(
			'number'=> true,
			'type'	=> 'select',
			'title' => 'Quyền hạn',
			'options' => $form->optRole(Url::getParamInt('role',0))
		), 'filter');
		$statusArr = array(1 => 'Bình thường', 8 => 'Đang Online', 9 => 'Không kích hoạt', 0 => 'Đã xóa');
		$form->layout->addItem('status', array(
			'number'=> true,
			'type'	=> 'select',
			'title' => 'Trạng thái',
			'options' => FunctionLib::getOption($statusArr, Url::getParamInt('status',1))
		), 'filter');
		//login time
		$form->layout->addItem('created_time', array(
			'type'	=> 'text',
			'title' => 'Từ ngày',
			'time'  => true,
			'holder'=> 'Ext: 30-07-2016',
			'value' => Url::getParam('created_time','')
		), 'login-time');
		$form->layout->addItem('created_time_to', array(
			'type'	=> 'text',
			'title' => 'Đến ngày',
			'time'  => true,
			'holder'=> 'Ext: 30-07-2016',
			'value' => Url::getParam('created_time_to','')
		), 'login-time');
		
		//add view table
		$form->layout->addItemView('btn-del-check', array(
			'per'	=>	$form->perm['del'],
			'type'	=>	'del',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('id', array(
			'title' => 'ID',
			'order' => true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('information', array(
			'title' => 'Thông tin chính'
		));
		$form->layout->addItemView('roles', array(
			'title' => 'Quyền hạn'
		));
		$form->layout->addItemView('province', array(
			'title' => 'Tỉnh thành'
		));
		$form->layout->addItemView('qrCode', array(
			'title' => 'QR Code',
			'per' => $form->perm['del'] && $data['log2step'] == 1
		));
		$form->layout->addItemView('logintab', array(
			'title' => 'Đăng nhập'
		));
		$form->layout->addItemView('online_icon', array(
			'title' => 'Status',
			'type'  =>	'icon',
			'only'	=>	true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('log', array(
			'title' => 'Log',
			'type'  =>	'icon',
			'per'   => $form->perm['log'],
			'icon' => 'event_note',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('active_icon', array(
			'title' => 'Active',
			'per'   => $form->perm['block'],
			'type'  => 'icon',
			'only'	=> true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('kick', array(
			'title' => 'Kick',
			'type'  =>	'icon',
			'per'   => $form->perm['edit'],
			'icon' => 'power_settings_new',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('cache', array(
			'title' => 'Cache',
			'type'  =>	'icon',
			'per'   => $form->perm['edit'],
			'icon' => 'cached',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-edit', array(
			'title' =>	'Sửa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['edit'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-del', array(
			'title' =>	'Xóa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['del'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		
		
		return $form->layout->genFormAuto($form, $data);
	}
	
	static function userAutoEdit(&$form, &$data = array(), $action = ''){
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST',
			'onsubmit'	=>	$form->action == 'edit' ? 'shop.onFormSubmit' : 'shop.submitForm'
		));
		
		//add group
		if($form->action == 'add'){
			$form->layout->addGroup('main', array('title' => 'Thông tin đăng nhập'));
		}else{
			$form->layout->addGroup('main', array('title' => 'Mật khẩu', 'toggle' => true, 'hide' => true));
		}
		$form->layout->addGroup('user-info', array('title' => 'Thông tin người dùng'));
		$form->layout->addGroup('user-city', array('title' => 'Tỉnh thành'));
		$form->layout->addGroup('user-role', array('title' => 'Nhóm quyền', 'per' => User::user_access('add role user')));
		
		//add form main
		if($form->action == 'add'){
			$form->layout->addItem('username', array(
				'type'	=> 'text',
				'title' => 'Tên đăng nhập',
				'required' => true,
				'ext' => array(
					'autocomplete' => 'off',
					'onkeyup' => "shop.reg_uname_press(this)"
				)
			), 'main');
		}else{
			$form->layout->addItem('user_id', array(
				'type'	=> 'hidden',
				'value' => $form->item['id'],
				'save'  => false
			), 'main');
			$form->layout->addItem('username', array(
				'type'	=> 'hidden',
				'value' => $form->item['username'],
				'save'  => false
			), 'main');
		}
		$form->layout->addItem('password', array(
			'type'	=> 'password',
			'title' => 'Mật khẩu',
			'ext' => array(
				'autocomplete' => 'off',
				'onkeyup' => "shop.reg_pass_press(this)"
			)
		), 'main');
		$form->layout->addItem('password1', array(
			'type'	=> 'password',
			'title' => 'Nhập lại mật khẩu',
			'save'  => false,
			'ext' => array(
				'autocomplete' => 'off'
			)
		), 'main');
		
		//add form info
		$form->layout->addItem('fullname', array(
			'type'	=> 'text',
			'title' => 'Họ và Tên',
			'value' => Url::getParam('fullname', $form->item['fullname'])
		), 'user-info');
		$form->layout->addItem('address', array(
			'type'	=> 'text',
			'title' => 'Địa chỉ',
			'value' => Url::getParam('address', $form->item['address'])
		), 'user-info');
		$form->layout->addItem('gender', array(
			'type'	=> 'radio-group',
			'title' => 'Giới tính',
			'value' => Url::getParam('gender', $form->item['gender']),
			'options' => array(0 => 'Nữ', 1 => 'Nam')
		), 'user-info');
		$form->layout->addItem('email', array(
			'type'	=> 'text',
			'title' => 'Email',
			'value' => Url::getParam('email', $form->item['email'])
		), 'user-info');
		$form->layout->addItem('mobile_phone', array(
			'type'	=> 'text',
			'title' => 'Điện thoại',
			'value' => Url::getParam('mobile_phone', $form->item['mobile_phone']),
			'ext' => array(
				'onkeypress' => 'return shop.numberOnly(this, event)',
				'maxlength'  => 11
			)
		), 'user-info');
		
		//add form tin thanh
		$province_active = array();
		foreach(CGlobal::$province_active as $i => $p){
			$province_active[$i] = $p['title'];
		}
		$defProvince = Url::getParam('province', $form->item['province']);
		if(!empty($defProvince)){
			if(is_string($defProvince)){
				$defProvince = explode(',', $defProvince);
			}
		}else{
			$defProvince = array();
		}
		$form->layout->addItem('province', array(
			'type'	=> 'checkbox-group',
			'title' => 'Chọn tỉnh thành',
			'value' => $defProvince,
			'options' => $province_active,
			'line' => false
		), 'user-city');
		
		//add role
		$roles = CGlobal::$permission_group;
		$cUser = User::$current->data;
		if(!User::is_big_boss() || !User::is_root()){
			$curRank = User::user_rank();
			$role_ids = $cUser['role_ids'];
			foreach($roles as $k => $r){
				if($curRank > $r['rank']){
					unset($roles[$k]);
				}elseif(!in_array($r['id'], $role_ids)){
					unset($roles[$k]);
				}
			}
		}
		$roleArr = array();
		foreach($roles as $k => $r){
			$roleArr[$r['id']] = $r['title'];
		}
		
		$form->layout->addItem('role_id', array(
			'type'	=> 'checkbox-group',
			'title' => 'Chọn quyền hạn',
			'value' => $form->item['role_ids'],
			'options' => $roleArr,
			'line' => false
		), 'user-role');
		
		if($action == 'draw'){
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
}
