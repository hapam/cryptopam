<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_user {
	function playme(){
		$code = Url::getParam('code');

		switch( $code ){
			case 'login_user':
				$this->login_user();
				break;
			case 'send-QR':
				$this->sendQR();
				break;
			case 'ignore-QR':
				$this->ignoreQR();
				break;
			case 'check_info':
				$this->check_pass_email();
			break;
			case 'valid_reg':
				$this->validRegister();
			break;
			case 'login_as':
				$this->login_as();
			break;
			case 'active-user':
				$this->activeUser();
			break;
			case 'change-pass':
				$this->changePassword();
				break;
			default: $this->home();
		}
	}

	function home(){
		global $display;
		die("Nothing to do...");
	}
	
	function sendQR(){
		if(User::user_access('delete user')){
			$uid = Url::getParam('uid', 0);
			if($uid > 0){
				$user = DB::fetch("SELECT * FROM " . T_USERS . " WHERE id=".$uid);
				if($user){
					$code = array('secret' => $user['secret'], 'qrCodeUrl' => $user['qrCodeUrl']);
					if ($user['secret'] == '' || $user['qrCodeUrl'] == '') {
						$code = Authentication::getQRcode_user();
						DB::update(T_USERS, $code, 'id=' . $user['id']);
					}
					$data = array('user' => $user, 'code' => $code);
					if (EmailLib::sendEmailQRCode($user['email'], $data)) {
						FunctionLib::JsonSuccess('success', false, true);
					}
					FunctionLib::JsonErr('not_send', false, true);
				}
			}
			FunctionLib::JsonErr('not_existed', false, true);
		}
		FunctionLib::JsonErr('access_dined', false, true);
	}
	
	function ignoreQR(){
		if(User::user_access('delete user')){
			$uid = Url::getParam('uid', 0);
			$action = Url::getParam('status', 0);
			if($uid > 0){
				$user = DB::fetch("SELECT * FROM " . T_USERS . " WHERE id=".$uid);
				if($user){
					DB::update(T_USERS, array('ignoreQR' => $action), "id = $uid");
					User::getUser($uid, true, true);
					FunctionLib::JsonSuccess('success', false, true);
				}
			}
			FunctionLib::JsonErr('not_existed', false, true);
		}
		FunctionLib::JsonErr('access_dined', false, true);
	}
	
	function login_as(){
		$id = Url::getParamInt('id', 0);
		$user = DB::fetch('SELECT * FROM '.T_USERS.' WHERE is_active = 1 AND status = 1 AND id='.$id);
		if($user){
			if(User::user_role_compare_byID($id)){
				CookieLib::my_setcookie('loginAs', $user['username']);
				logCenter::set('user', 'login_as', 0, $id);//ghi lai log
				FunctionLib::JsonSuccess('success', array('url' => Url::build('admin')), true);
			}
			FunctionLib::JsonErr('permiss', false, true);
		}
		FunctionLib::JsonErr('fail', false, true);
	}
	
	function login_user(){
		$username = trim(Url::getParam('user',''));
		$pass = Url::getParam('pass','');
		$newpass = Url::getParam('newpass','');
		$captcha = Url::getParamInt('captcha', 0);
		
		if (strlen($username) <3  || strlen($username) >50  || preg_match('/[^A-Za-z0-9_\.@]/',$username) || strlen($pass)<5){
			FunctionLib::JsonErr('nodata', false, true);
		} else{
			if($captcha == 1){
				require_once(ROOT_PATH."includes/recaptcha/recaptchalib.php");
				$privatekey = CGlobal::$captchaPrivate;
				$resp = recaptcha_check_answer ($privatekey,
					$_SERVER["REMOTE_ADDR"],
					$_POST["recaptcha_challenge_field"],
					$_POST["recaptcha_response_field"]);
			
				if (!$resp->is_valid) {
					FunctionLib::JsonErr('captcha', array('error' => $resp->error ), true);
				}
			}
			
			$user = preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/",$username) ? addslashes($username) : str_replace(array('"','\\'),'_',$username);
			$user_data = DB::fetch("SELECT * FROM ".T_USERS." WHERE username='$user'");
			if($user_data && $user_data['password'] == User::encode_password($pass)){
				if((isset($user_data['block_id']) && intval($user_data['block_id']) > 0) || $user_data['status'] <= 0){
					FunctionLib::JsonErr('blocked', false, true);
				}
				if(USER_ACTIVE_ON && !$user_data['is_active']){//Chưa kích hoạt
					FunctionLib::JsonErr('un_active', false, true);
				}
				//neu lan dau tien dang nhap bat doi mat khau
				if(($user_data['last_login'] == 0) && ($newpass == '')){
					FunctionLib::JsonErr('change_pass', array('day_change' => 0, 'last' => '', 'first_login' => 1), true);
				}else{
					//neu sau 1 khoang thoi gian chua doi mat khau thi check
					if(($user_data['last_login'] != 0) && (CGlobal::$changePassTime > 0) && (time() - $user_data['last_changepass'] > CGlobal::$changePassTime*3600*24) && ($newpass == '')){
						$time = $user_data['last_changepass'] > 0 ? date('d-m-Y', $user_data['last_changepass']) : "";
						FunctionLib::JsonErr('change_pass', array('day_change' => CGlobal::$changePassTime, 'last' => $time), true);
					}

					if ($newpass != '') {						
						if ($newpass == $pass) {
							FunctionLib::JsonErr('other_new_pass', false, true);
						} elseif (FunctionLib::isNotSafePassword($newpass)) {
							FunctionLib::JsonErr('invalid_new_pass', false, true);
						} elseif (strlen($newpass) < 5) {
							FunctionLib::JsonErr('invalid_new_pass', false, true);
						} else {
							DB::update(T_USERS, array('last_changepass' => time(), 'password' => User::encode_password($newpass)), 'id='.$user_data['id']);
						}
					}
					$log2step = ConfigSite::getConfigFromDB('log2step', 0, false, 'site_configs');
					$noNeed2Step = false;
					if($log2step == 0 || $user_data['ignoreQR'] == 1){
						$noNeed2Step = true;
					}elseif($log2step == 1){
						$noNeed2Step = Authentication::checkValid2Step($user_data['id']);
					}
					if($noNeed2Step){
						if(Url::getParam('set_cookie')=='on'){
							CookieLib::my_setcookie(md5("password"), $user_data['password']);
						}
						User::LogIn($user_data['id'], true);
						$user_data['url_next'] = Url::build('admin');
					}else{
						CookieLib::my_setcookie(md5("id_user_forstep2"), FunctionLib::hiddenID($user_data['id']), 86400 + TIME_NOW);
						$user_data['url_next'] = Url::build('admin_login', array('cmd' => 'authenticator'));
					}
					$_SESSION['error_pass_login'] = 0;
					FunctionLib::JsonSuccess('success', array('url_next' => $user_data['url_next']), true);
				}
			}
			else{
				if(!isset($_SESSION['error_pass_login'])){
					$_SESSION['error_pass_login'] = 0;
				}
				$_SESSION['error_pass_login']++;
				$data = array('captcha' => CGlobal::$adminCaptcha, 'number_error' => ConfigSite::getConfigFromDB('captcha_error', 1, false, 'site_configs'), 'wrong' => $_SESSION['error_pass_login']);
				if($user_data){
					FunctionLib::JsonErr('err_pass', $data, true);
				}
				FunctionLib::JsonErr('err_user', $data, true);
			}
		}
	}

	function check_pass_email(){
		if(User::is_admin()){
			$email = strtolower(Url::getParam('email',''));
			$pass  = Url::getParam('old_pass','');
			$uname = Url::getParam('uname','');
			$uid   = Url::getParamInt('uid',0);
			$user  = (User::$current->data['id'] == $uid) ? User::$current->data : User::getUser($uid);
	
			//check email
			if($email != ''){
				$sql = sprintf("SELECT email, id FROM " . T_USERS . " WHERE id != %d AND email = '%s' LIMIT 0,1",$user['id'], $email);
				$row  = DB::fetch($sql);
				if($row && $uid != $row['id']){
					FunctionLib::JsonErr('email_existed', array('email' => "Email đã được sử dụng"), true);
				}
			}
			FunctionLib::JsonSuccess('success', false, true);
		}
		FunctionLib::JsonErr('access_dined', false, true);
	}
	
	function validRegister(){
		if(User::user_access('edit user') || User::user_access('add user')){
			$msg = array('email' => null, 'uname' => null);
			$uname = strtolower(Url::getParam('uname',''));
			$email = strtolower(Url::getParam('email',''));
			$sql = sprintf("SELECT username, email FROM " .T_USERS." WHERE LOWER(username) = '%s' OR email = '%s'", $uname, $email);
			$re  = DB::query($sql);
			while($r = mysql_fetch_object($re)){
				if($r->email == $email && !isset($msg['email'])){
					$msg['email'] = 'Email đã sử dụng';
				}
				if(strtolower($r->username) == $uname && !isset($msg['username'])){
					$msg['uname'] = 'Tên đăng nhập đã sử dụng';
				}
			}
			if(!empty($msg['email']) || !empty($msg['uname'])){
				FunctionLib::JsonErr('error', $msg, true);
			}
			FunctionLib::JsonSuccess('success', $msg, true);
		}
		FunctionLib::JsonErr('access_dined', false, true);
	}
	
	function activeUser(){
		if(User::user_access('edit user')){
			$curID = User::id();
			$active = Url::getParamInt('is_active', -1);
			$id = Url::getParamInt('id', 0);
			if($id > 0 && $active >=0){
				if($curID != $id){
					if($id > 1){
						if(User::user_role_compare_byID($id)){
							$active = ($active == 1) ? 0 : 1;
							if(DB::update(T_USERS,array('is_active' => $active),"id=$id")){
								User::getUser($id, true, true);
								echo FunctionLib::JsonSuccess('User đã được chuyển trạng thái kích hoạt',array('active' => $active, 'id' => $id));
							}else{
								echo FunctionLib::JsonErr('Lỗi DB');
							}
						}else{
							echo FunctionLib::JsonErr('Không được thay đổi trạng thái người có quyền cao hơn');
						}
					}else{
						echo FunctionLib::JsonErr('Không được thay đổi trạng thái admin tổng');
					}
				}else{
					echo FunctionLib::JsonErr('Không được tự thay đổi trạng thái của mình');
				}
			}else{
				echo FunctionLib::JsonErr('Có lỗi xảy ra! Dữ liệu không hợp lệ');
			}
		}else{
			echo FunctionLib::JsonErr('Bạn không có quyền thay đổi thông tin người dùng');
		}
	}
	
	function changePassword(){
		$old = Url::getParam('old');
		$new = Url::getParam('news');
		$valid = Url::getParam('valid');
		$user = User::$current->data;
		if($user['password'] == User::encode_password($old)){
			if($new == $valid){
				DB::update(T_USERS, array('last_changepass' => time(), 'password' => User::encode_password($new)), 'id='.$user['id']);
				User::getUser($user['id'], true, true);
				echo FunctionLib::JsonSuccess('success');
			}else{
				echo FunctionLib::JsonErr('not_equal');
			}
		}else{
			echo FunctionLib::JsonErr('old_error');
		}
	}
}//class
