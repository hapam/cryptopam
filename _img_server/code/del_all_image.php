<?php
	if(!isset($s)){
		require_once('config.inc.php');
		$s = new SuperImageServer();
		server_delete_all_image($s);
	}
	
	function server_delete_all_image(&$s){
		empty_all_dir(substr(ROOT,0,-1), true, true);

		$error = $s->setSuccess("OK");

		//return result
		$client = $s->getParam('from_client', 0);
		if($client == 0){
			die($error);
		}
		$s->msg = $error;
	}
	
	function empty_all_dir($name, $remove_sub_dir = false,$remove_self=false){
		if(is_dir($name)){
			if($dir = opendir($name)){
				$dirs = array();
				while($file=readdir($dir)){
					if($file!='..' && $file!='.' && $file!='.htaccess' && $file!='code'){
						if(is_dir($name.'/'.$file)){
							$dirs[]=$file;
						}
						else{
							//kiem tra neu ko co original thi xoa
							if (stripos($name, 'origin') === false) {
								@unlink($name.'/'.$file);
							}
						}
					}
				}
				closedir($dir);
				foreach($dirs as $dir_){
					empty_all_dir($name.'/'.$dir_, ($remove_self || $remove_sub_dir),($remove_self || $remove_sub_dir));
				}
				if($remove_self){
					@rmdir($name);
				}
			}
		}
	}
