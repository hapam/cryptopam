<?php

if (preg_match("/" . basename(__FILE__) . "/", $_SERVER ['PHP_SELF'])) {
    die("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_signup {

    function playme() {
        $code = Url::getParam('code');

        switch ($code) {
            case 'reg':
                $this->register();
                break;
            default: $this->home();
        }
    }
    
    function register(){
        $msg = array('email' => '', 'uname' => '', 'error' => '');
		$uname = strtolower(Url::getParam('uname',''));
		$email = strtolower(Url::getParam('email',''));
        $pass = Url::getParam('password','');
		$sql = sprintf("SELECT username, email FROM " .T_USERS." WHERE LOWER(username) = '%s' OR email = '%s'", $uname, $email);
        $re  = DB::query($sql);
        while($r = mysql_fetch_object($re)){
            if($r->email == $email){
                $msg['email'] = 'Email has been used';
            }
            if(strtolower($r->username) == $uname){
                $msg['uname'] = 'Username has been used';
            }
        }
        if($msg['email'] != '' || $msg['uname'] != ''){
            FunctionLib::JsonErr('error', $msg, true);
        }
        $rid = 3;//user
        $data = array(
            'email' => $email,
            'username' => $uname,
            'fullname' => $uname,
            'password' => User::encode_password($pass),
            'created' => time(),
			'is_active' => 1,
            'role_id' => $rid
        );
        $id = DB::insert(T_USERS, $data);
        if($id > 0){
            DB::insert(T_USER_ROLES, array("uid" => $id, "rid" => $rid));
            User::LogIn($id, true);
            FunctionLib::JsonSuccess("ok", array('url' => Url::build('home')), true);
        }
        $msg['error'] = 'Database error!';
        FunctionLib::JsonErr('error', $msg, true);
    }
    
    function home() {
        die("Nothing to do...");
    }
}