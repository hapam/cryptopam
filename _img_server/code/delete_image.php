<?php
	if(!isset($s)){
		require_once('config.inc.php');
		$s = new SuperImageServer();
		server_delete_image($s);
	}
	
	function server_delete_image(&$s){
		$file_name = $s->getParam('file_name');
		$time = $s->getParam('time');
		$type = $s->getParam('type');
	
		$error = '';
		if($file_name != '' && $type != '' && $time != ''){
			if(isset($s->dataImage[$type])){
				$sizes = $s->dataImage[$type]['sizes'];
				$folder= $s->dataImage[$type]['folder'];
				
				$rootDir   = ROOT . $folder . $s->createdDirByTime($time);
				$originDir = $rootDir . ORIGIN_FOLDER;
				$desDir    = $rootDir . FOLDER_PREFIX;
	
				//xoa toan bo file
				$s->removeFileBySize($file_name, $sizes, $desDir);
	
				//xoa file goc
				$file_name = $originDir . '/' . $file_name;
				if(is_file($file_name)){
					@unlink($file_name);
				}
	
				//thanh cong
				$error = $s->setSuccess("OK");
			}else{
				$error = $s->setError('NO_DATA_IMG');
			}
		}else{
			$error = $s->setError('FILE_ERROR');
		}
	
		//return result
		$client = $s->getParam('from_client', 0);
		if($client == 0){
			die($error);
		}
		$s->msg = $error;
	}
