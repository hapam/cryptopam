<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_admin {
	function playme(){
		$code = Url::getParam('code');

		switch( $code ){
			case 'delcache':
				$this->delcache();
			break;
			case 'showCache':
				$this->showCache();
			break;
			case 'log':
				$this->showLog();
				break;
			case 'site-online':
				$this->checkSiteOnline();
				break;
			
			
			default: $this->home();
		}
	}
	
	function delcache(){
		if(User::is_root()){
			$cacheKey = Url::getParam('cacheKey','');
			$cacheDir = Url::getParam('cacheDir','');
			if($cacheKey){
				$return = CacheLib::delete($cacheKey, $cacheDir);
			}
			if($return){
				FunctionLib::JsonSuccess($return,array('hashKey' => md5($cacheKey)), true);
			}
			else{
				FunctionLib::JsonErr('Không xoá được cache', false, true);
			}
		}
		FunctionLib::JsonErr('Không có quyền', false, true);
	}
	function showCache(){
		if(User::is_root()){
			$cacheKey = Url::getParam('cacheKey','');
			$cacheTime = Url::getParam('cacheTime',0);
			$cacheDir = Url::getParam('cacheDir','');
			if($cacheKey){
				$return = CacheLib::get($cacheKey, 0, $cacheDir);
			}
			if($return){
				FunctionLib::JsonSuccess($return,array('hashKey' => md5($cacheKey)),true);
			}
			else{
				FunctionLib::JsonErr('Không tồn tại cache', false, true);
			}
		}
		FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function checkSiteOnline(){
		$check = OFF_SITE == 0 && CGlobal::$web_status == 'online';
		if($check){
			FunctionLib::JsonSuccess(Url::buildURL(CGlobal::$defaultHomePage),false,true);
		}else{
			echo FunctionLib::JsonErr('error '.CGlobal::$web_status,false,true);
		}
	}
	
	function showLog(){
		$type = Url::getParam('type');
		$id = Url::getParamInt('id', 0);
		$total = Url::getParamInt('total_page', 1);
		$page = Url::getParamInt('page', 1);
		$itemperpage = Url::getParamInt('recperpage', 10);
		if($page < 1){
			$page = 1;
		}elseif($page > $total){
			$page = $total;
		}
		$from = ($page - 1)*$itemperpage;
		$to = $from+$itemperpage;
		$data = logCenter::fetchLog($id, $type, $from, $to);
		if(is_array($data)){
			FunctionLib::JsonSuccess('Thành công', $data, true);
		}
		$msg = ($data == 0) ? 'Dữ liệu đã bị xóa hoặc không tồn tại' : 'Không có quyền thực hiện thao tác';
		FunctionLib::JsonErr($msg, false, true);
	}
	
	function home(){
		die("Nothing to do...");
	}
}//class
