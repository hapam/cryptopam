<?php
class EditUserForm extends Form{
	private $table = T_USERS, $table_role = T_USER_ROLES;
	public $item, $action, $id;
	function __construct(){
		parent::__construct();
		$this->id  = Url::getParamInt('id', 0);
		$this->action = Url::getParamAdmin('action', 'add');

		if($this->id > 0){
			if(!User::user_role_compare_byID($this->id)){
				exit('<h1 align="center">Không có quyền truy cập vào thông tin của người có quyền ngang bằng hoặc cao hơn<br /> <a href="javascript:history.go(-1)">Quay lại</a></h1>');
			}
			$this->item = User::getUser($this->id, true);
			$this->link_js_me('userEdit.js', __FILE__);
		}else{
			$this->link_js_me('userRegister.js', __FILE__);
		}
		$this->link_js("js/ext/password.js");
	}

	function draw(){
		$data = array();
		User::userAutoEdit($this, $data, 'draw');
	}

	function on_submit(){
		$data = array();
		if(User::userAutoEdit($this, $data, 'submit')){
			$password = Url::getParam('password1');
			$role = $data['role_id'];
			if(empty($role)){
				$this->setFormError('role_id', 'Bạn chưa chọn nhóm quyền');
			}
			if($this->id<=0 && $this->isExisted($data['username'], $data['email'])){
				$this->setFormError('username', 'Tên đăng nhập hoặc email đã tồn tại');
			}
			
			$log = array();
			if($this->errNum == 0){
				$data['province'] = $this->orderProvince($data['province']);
				$data['province'] = implode(',',$data['province']);
				if($data['password'] != '' && $password != ''){
					$log[] = 'pass';
					$data['password'] = User::encode_password($data['password']);
				}
				else {
					unset($data['password']);
				}
				if($this->id<=0){
					$data['created'] = time();
					$data['is_active'] = 1;
					$this->id = DB::insert($this->table, $data);
				}
				if($this->id>0){
					if(User::user_role_compare_byID($this->id)){
						//insert Roles
						if(!empty($role)){
							DB::delete($this->table_role, "uid=".$this->id);
							foreach($role as $rid){
								DB::insert($this->table_role, array("uid" => $this->id, "rid" => $rid));
							}
						}
						//update du lieu
						DB::update($this->table, $data, "id=".$this->id);
						
						//ghi log
						logCenter::set('user', $log);
						
						//build lai cached
						User::getUser($this->id, false, true);
					}else{
						$this->setFormError('', 'Bạn không có quyền thay đổi thông tin của người có quyền cao hơn mình');
					}
				}
				Url::redirect('admin', array('cmd' => 'user'));
			}
		}
		$this->setFormError('', 'Không lưu được thông tin người dùng');
	}
	
	function isExisted($user_name = '', $email = ''){
		if($user_name != '' || $email  != ''){
			$sql = "SELECT * FROM ".$this->table." WHERE status = 1";
			$more = '';
			if($user_name != ''){
				$more = ($email  != '') ? "(username = '$user_name' || email = '$email')" : "username = '$user_name'";
			}else{
				$more = "email = '$email'";
			}
			$user = DB::fetch($sql." AND ".$more);
			if($user){
				return true;
			}
		}
		return false;
	}
	
	function orderProvince($province = array()){
		$length = count($province);
		if(!empty($province) && $length > 1){
			$temp = array();
			foreach($province as $v){
				$temp[] = $v;
			}
			$province = $temp;
			for($i=0;$i<$length;$i++){
				for($j=$i+1;$j<$length;$j++){
					if($province[$i] > $province[$j]){
						$temp = $province[$i];
						$province[$i] = $province[$j];
						$province[$j] = $temp;
					}
				}
			}
		}
		return $province;
	}
}
