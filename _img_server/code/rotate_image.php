<?php
	if(!isset($s)){
		require_once('config.inc.php');
		$s = new SuperImageServer();
		server_rotate_image($s);
	}
	
	function server_rotate_image(&$s){
		$file_name = $s->getParam('file_name');
		$time = $s->getParam('time');
		$type = $s->getParam('type');
		$degrees = $s->getParam('degrees');
		
		$error = '';
		if($file_name != '' && $time != ''){
			if(isset($s->dataImage[$type])){
				$error = $s->imageRotate($file_name, $time, $type, $degrees, 0, $s->dataImage[$type]['mask'] == 1);
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
