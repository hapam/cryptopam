<?php
	if(!isset($s)){
		require_once('config.inc.php');
		$s = new SuperImageServer();
		server_resize_image($s);
	}
	
	function server_resize_image(&$s){
		$file_src  = $s->getParam('file_path');
		$file_name = $s->getParam('file_name');
		$old_file  = $s->getParam('old_file');
		$time = $s->getParam('time');
		$type = $s->getParam('type');
		
		$error = '';
		if($file_src != '' && $file_name != '' && $time != ''){
			if(isset($s->dataImage[$type])){
				$error = $s->createImageBySizes($file_src, $file_name, $time, $s->dataImage[$type]['folder'], $s->dataImage[$type]['sizes'], $old_file, $s->dataImage[$type]['mask'] == 1);
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
