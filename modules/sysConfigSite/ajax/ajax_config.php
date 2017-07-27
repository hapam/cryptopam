<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_config {
	function playme(){
		$code = Url::getParam('code');

		switch( $code ){
            case 'add-dir':
				$this->addDir();
				break;
			case 'del-dir':
				$this->delDir();
				break;
			case 'add-size':
				$this->addSize();
				break;
			case 'del-size':
				$this->delSize();
				break;
			case 'remove-img':
				$this->removeImg();
				break;
			case 'build':
				$this->build();
				break;
			case 'global':
				$this->cglobal();
				break;
			case 'global-use':
				$this->cglobalUse();
				break;
			default: $this->home();
		}
	}
	
	function addDir(){
        if(User::user_access('config site')) {
			$key = 'imageSize';
			$name = Url::getParam('name', '');
			$defi = Url::getParam('defi', '');
			$mask = Url::getParamInt('mask', 0);
			$oldKey = Url::getParam('oldKey', '');
            if ($name != '' && $defi != '') {
				$imgSize = ConfigSite::getConfigFromDB($key, array(), true);
				$new = array(
					'name' => $name,
					'defined' => $defi,
					'wm' => $mask,
					'sizes' => array()
				);
				if($oldKey != ''){
					//kiem tra xem key moi da co chua
					if($oldKey != $name && isset($imgSize[$name])){
						FunctionLib::JsonErr('Thư mục đã tồn tại', false, true);
					}
					if(isset($imgSize[$oldKey])){
						$tmp = $imgSize[$oldKey];
						$new['sizes'] = $tmp['sizes'];
						if($oldKey != $name){
							unset($imgSize[$oldKey]);
						}
					}
				}elseif(isset($imgSize[$name])){
					FunctionLib::JsonErr('Thư mục đã tồn tại', false, true);
				}
				$imgSize[$name] = $new;
				ConfigSite::setConfigToDB($key, serialize($imgSize));
                FunctionLib::JsonSuccess('success', false, true);
            }
            FunctionLib::JsonErr('Thiếu thông tin', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function delDir(){
        if(User::user_access('config site')) {
			$name = Url::getParam('key', '');
			if(ImageUrl::removeFolderImg($name)){
			    FunctionLib::JsonSuccess('success', false, true);
            }
            FunctionLib::JsonErr('Thiếu thông tin', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function removeImg(){
        if(User::user_access('config site')) {
			if(IS_UPLOAD_IMAGE_SERVER){
				$curl = new CURL();
				$obj  = $curl->post(ImageUrl::getImageServerUrl() . IMAGE_CODE_DIR . 'del_all_image.php', array());
			}else{
				//sinh anh tren cung 1 server
				$vars['from_client'] = 1;
				$imgCodePath = ROOT_PATH.IMAGE_PATH_STATIC.IMAGE_CODE_DIR;
				require_once($imgCodePath.'config.inc.php');
				$s = new SuperImageServer();
				$s->param = $vars;

				//goi ham sinh anh & luu ket qua
				require_once($imgCodePath.'del_all_image.php');
				server_delete_all_image($s);
				$obj = $s->msg;
			}
			$result  = @unserialize($obj);
			if($result && $result['err'] == 0){
				FunctionLib::JsonSuccess('success', $result, true);
			}
            FunctionLib::JsonErr('error', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function addSize(){
        if(User::user_access('config site')) {
			$key = 'imageSize';
			$w = Url::getParam('w', 0);
			$h = Url::getParam('h', 0);
			$name = Url::getParam('key', '');
            if ($w > 0 || $h > 0) {
				$imgSize = ConfigSite::getConfigFromDB($key, array(), true);
				if(isset($imgSize[$name])){
					$k = $w > 0 ? $w : $h;
					$imgSize[$name]['sizes'][$k] = array(
						'w' => $w,
						'h' => $h
					);
					ConfigSite::setConfigToDB($key, serialize($imgSize));
					FunctionLib::JsonSuccess('success', false, true);
				}
				FunctionLib::JsonErr('Không tìm thấy thư mục', false, true);
            }
            FunctionLib::JsonErr('Thiếu thông tin', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function delSize(){
        if(User::user_access('config site')) {
			$key = 'imageSize';
			$w = Url::getParam('w', 0);
			$name = Url::getParam('key', '');
            if ($w > 0 && $name != '') {
				$imgSize = ConfigSite::getConfigFromDB($key, array(), true);
				if(isset($imgSize[$name])){
					unset($imgSize[$name]['sizes'][$w]);
					ConfigSite::setConfigToDB($key, serialize($imgSize));
					FunctionLib::JsonSuccess('success', false, true);
				}
				FunctionLib::JsonErr('Không tìm thấy thư mục', false, true);
            }
            FunctionLib::JsonErr('Thiếu thông tin', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function build(){
        if(User::user_access('config site')) {
			$client = ConfigSite::writeConfigImage();
			FunctionLib::JsonSuccess('Đã tạo file', array('file' => $client), true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function home(){die("Nothing to do...");}
	
	function cglobalUse(){
        if(User::user_access('config site')) {
			$key = Url::getParam('key','');
			if($key != ''){
				$data = array();
				$this->checkCGlobalUse($key, substr(DIR_MODULE,0,-1), $data);
				$this->checkCGlobalUse($key, substr(DIR_THEMES,0,-1), $data);
				
				FunctionLib::JsonSuccess('success', array('data' => $data), true);
			}
			FunctionLib::JsonErr('Dữ liệu tìm kiếm sai', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function checkCGlobalUse($search = '', $name = '', &$data = array()){
		if(is_dir($name)){
			if($dir = opendir($name)){
				$dirs = array();
				while($file=readdir($dir)){
					if($file!='..' && $file!='.' && $file!='sysConfigSite'){
						if(is_dir($name.'/'.$file)){
							$dirs[]=$file;
						}
						else{
							//kiem tra neu laf file php
							if (stripos($file, '.php') !== false) {
								$key = explode('modules/', $name);
								$key = explode('/',$key[1]);
								$key = $key[0];

								$content = file_get_contents($name.'/'.$file);
								$content   = explode("\n", $content); 
								for ($line = 0; $line < count($content); $line++) { 
									if (stripos($content[$line], "('$search'") !==false || stripos($content[$line], '("'.$search.'"') !== false) {
										if(!isset($data[$key])){
											$data[$key] = array();
										}
										if(!isset($data[$key][$file])){
											$data[$key][$file] = array(
												'direct' => $name,
												'line'  => array()
											);
										}
										$data[$key][$file]['line'][] = $line+1;
									} 
								}
							}
						}
					}
				}
				closedir($dir);
				foreach($dirs as $dir_){
					$this->checkCGlobalUse($search, $name.'/'.$dir_, $data);
				}
			}
		}
		return $data;
	}
	
	function cglobal(){
        if(User::user_access('config site')) {
			$data = array();
			
			$this->checkCGlobal(substr(DIR_MODULE,0,-1), $data);
			$this->checkCGlobal(substr(DIR_THEMES,0,-1), $data);
			
			FunctionLib::JsonSuccess('success', array('data' => $data), true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}
	
	function checkCGlobal($name = '', &$data = array()){
		if(is_dir($name)){
			if($dir = opendir($name)){
				$dirs = array();
				while($file=readdir($dir)){
					if($file!='..' && $file!='.' && $file!='sysConfigSite'){
						if(is_dir($name.'/'.$file)){
							$dirs[]=$file;
						}
						else{
							//kiem tra neu laf file php
							if (stripos($file, '.php') !== false) {
								$content = file_get_contents($name.'/'.$file);
								$pattern = '/CGlobal::set\(.*\)+/i';
								preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
								if(!empty($matches[0])){
									$key = explode('modules/', $name);
									$key = explode('/',$key[1]);
									$key = $key[0];
									
									if(!isset($data[$key])){
										$data[$key] = array();
									}
									if(!isset($data[$key][$file])){
										$data[$key][$file] = array(
											'direct' => $name,
											'found'  => array()
										);
									}

									foreach($matches[0] as $m){
										$str = $m[0];
										$str = str_replace(array('CGlobal::set(', ')'), array('',''), $str);
										$str = explode(',', $str);
										foreach($str as $k => $v){
											$v = trim($v);
											if($v[0] == "'" || $v[0] == '"'){
												$v = substr($v,1,-1);
											}
											if(stripos($v, '$') !== false){
												$p = '/\$'.substr($v,1).'[ \=](.*?);[^a-zA-Z0-9]/i';
												preg_match_all($p, $this->test_input($content), $m, PREG_OFFSET_CAPTURE);
												if(!empty($m[0])){
													$code = str_replace(substr($v,1), "v", $this->test_input($m[0][0][0], true));
													@eval($code);
												}
											}
											$str[$k] = $v;
										}
										$data[$key][$file]['found'][] = $str;
									}
								}
							}
						}
					}
				}
				closedir($dir);
				foreach($dirs as $dir_){
					$this->checkCGlobal($name.'/'.$dir_, $data);
				}
			}
		}
		return $data;
	}
	
	function test_input($data, $decode = false) {
		$data = trim($data);
		if($decode){
			$data = stripslashes(str_replace(array('\\r\\n','\t'),array('',''),stripslashes($data)));
		}else{
			$data = json_encode($data, JSON_UNESCAPED_UNICODE );
			$data = addslashes($data);
		}
		return $data;
	}
	
}//class
