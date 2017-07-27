<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_backup {
	function playme() {
		$code = Url::getParam('code');
		switch( $code ) {
			case 'add':
				$this->createBackup();
				break;
			case 'restore':
				$this->restoreBackup();
				break;
			case 'del':
				$this->delBackup();
				break;
			default: $this->home();
		}
	}
	
	function delBackup() {
		if(User::user_access('delete backup')){
			$id = Url::getParamInt('id', 0);
			if($id > 0){
				$backup = DB::fetch("SELECT * FROM ".T_BACKUP." WHERE id = $id");
				if(!empty($backup)){
					$file = Backup::getLinkDir($backup['name'], $backup['created'], false);
					if(FileHandler::delete($file)){
						DB::delete(T_BACKUP, "id=$id");
						FunctionLib::JsonSuccess('Đã xóa backup '.$backup['name'], array(), true);
					}
					FunctionLib::JsonErr('Lỗi!!! Không xóa được file. Hãy thử lại', array(), true);
				}
			}
			FunctionLib::JsonErr('Lỗi!!! Không tìm thấy bản backup', array(), true);
		}
		FunctionLib::JsonErr('access_denied', array(), true);
	}
	
	function restoreBackup() {
		if(User::user_access('restore backup')){
			$id = Url::getParamInt('id', 0);
			if($id > 0){
				$backup = DB::fetch("SELECT * FROM ".T_BACKUP." WHERE id = $id");
				if(!empty($backup)){
					$msg = '';
					if(DB::import(Backup::getLinkDir($backup['name'], $backup['created']), $msg)){
						//xoa cache all
						FunctionLib::JsonSuccess('Đã khôi phục dữ liệu ngày '.FunctionLib::dateFormat($backup['created'], 'd/m/Y - H:i:s'), array(), true);
					}
					FunctionLib::JsonErr('Lỗi!!! Khôi phục không thành công. '.$msg, array(), true);
				}
			}
			FunctionLib::JsonErr('Lỗi!!! Không tìm thấy bản backup', array(), true);
		}
		FunctionLib::JsonErr('access_denied', array(), true);
	}
	
	function createBackup() {
		if(User::user_access('restore backup')){
			$time = TIME_NOW;
			$name = '';
			if(DB::export(Backup::getDir($time), $name)){
				DB::insert(T_BACKUP, array('name' => $name, 'created' => $time));
				FunctionLib::JsonSuccess('Backup đã được tạo vào ngày '.FunctionLib::dateFormat($time, 'd/m/Y - H:i:s'), array(), true);
			}
			FunctionLib::JsonErr('Lỗi!!! Tạo file backup không thành công', array(), true);
		}
		FunctionLib::JsonErr('access_denied', array(), true);
	}
	
	function home() {
		die("Nothing to do...");
	}
}