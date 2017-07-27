<?php
class FileHandler{
	
	static function isImageFile($fname = ''){
		return preg_match("#.*\.(?:jpg|gif|png|jpeg)$#", strtolower($fname)) > 0;
	}
	static function isFaviconFile($fname = ''){
		return preg_match("#.*\.(?:jpg|gif|png|ico)$#", strtolower($fname)) > 0;
	}
	static function isBannerFile($fname = ''){
		return preg_match("#.*\.(?:jpg|gif|png|mp4|flv|swf)$#", strtolower($fname)) > 0;
	}
	static function copyImageFromUrl($url,$fileName, $time, $sizeKey, $folderUpload, &$err, $old_image){
        $size = FileHandler::curl_get_file_size($url);
        $name = StringLib::safe_title(microtime()).'-'.basename($url);
        $file = array('tmp_name' => $url,'size'=>$size,'name'=>$name);

        return FileHandler::resizeImageOnServer($file, $fileName, $time, $sizeKey, $folderUpload, $err, $old_image,true);
    }
	static function resizeImageOnServer($fileObj = false, $fname = '', $time = '', $sizeKey = '', $folder = '', &$err = '', $old_file = '',$copy = false){
		if($fileObj && $fname != '' && $folder != '' && $time != '' && $sizeKey != '' && isset(CGlobal::$imageSizeKeys[$sizeKey])){
			if(isset($fileObj['size']) && $fileObj['size'] > 0){
				if($fileObj['size'] > CGlobal::$max_upload_size){
					$err = 'Dung lượng file không được vượt quá <b>'.floor(CGlobal::$max_upload_size/(1024*1024)).'MB</b>';
				}elseif(!FileHandler::isImageFile($fileObj['name'])){
					$err = "Sai định dạng ảnh, hệ thống chỉ chấp nhận: JPG, PNG, GIF";
				}else{
					$fname = FileHandler::getNameByTime($fileObj['name'], $tail, $fname);
					$desName = $folder.'tmp/'.$fname;
					if(FileHandler::upload($fileObj['tmp_name'], IMAGE_SERVER_TEMP_PATH.$desName,$copy)){
						$url  = ImageUrl::getImageServerUrl() . IMAGE_CODE_DIR . 'resize_image.php';
						$vars = array(
							'file_path'	=>	$desName,
							'file_name' =>	$fname,
							'old_file'  =>	$old_file,
							'time' => $time,
							'type' => $sizeKey
						);
						if(IS_UPLOAD_IMAGE_SERVER){
							$curl = new CURL();
							$obj  = $curl->post($url, $vars);
						}else{
							//sinh anh tren cung 1 server
							$vars['from_client'] = 1;
							$imgCodePath = ROOT_PATH.IMAGE_PATH_STATIC.IMAGE_CODE_DIR;
							require_once($imgCodePath.'config.inc.php');
							$s = new SuperImageServer();
							$s->param = $vars;
							//goi ham sinh anh & luu ket qua
							require_once($imgCodePath.'resize_image.php');
							server_resize_image($s);
							$obj = $s->msg;
						}
						$result  = @unserialize($obj);
						if($result && $result['err'] == 0){
							$err = $result['msg'];
							//delete old file from DB
							if($old_file != ''){
								DB::delete(T_FILE_UPLOAD, "file = '$old_file' AND time = $time");
							}
							//insert DB file to DB
							foreach(CGlobal::$imageSizeKeys[$sizeKey] as $s){
								DB::insert(T_FILE_UPLOAD, array('file' => $err, 'size' => $s, 'time' => $time, 'type' => $sizeKey, 'created' => TIME_NOW));
							}
							return true; //success upload image & resize on server
						}else{
							$err = "Lỗi khi upload ảnh. Chi tiết: ".$obj;
						}
					}else{
						$err = "Không upload được ảnh lên server";
					}
				}
			}
			//else{
			//	$err = "File rỗng";
			//}
		}
		//else{
		//	$err = "File lỗi";
		//}
		return false;
	}
    
	static function rotateImageOnServer($fname = '', $time = '', $sizeKey = '', $degrees = 0, &$err = ''){
		if($fname != '' && $time != '' && $sizeKey != '' && isset(CGlobal::$imageSizeKeys[$sizeKey])){

			$url  = ImageUrl::getImageServerUrl() . IMAGE_CODE_DIR . 'rotate_image.php';
			$vars = array(
				'file_name' =>	$fname,
				'degrees'  =>	$degrees,
				'time' => $time,
				'type' => $sizeKey
			);
			if(IS_UPLOAD_IMAGE_SERVER){
				$curl = new CURL();
				$obj  = $curl->post($url, $vars);
			}else{
				//sinh anh tren cung 1 server
				$vars['from_client'] = 1;
				$imgCodePath = ROOT_PATH.IMAGE_PATH_STATIC.IMAGE_CODE_DIR;
				require_once($imgCodePath.'config.inc.php');
				$s = new SuperImageServer();
				$s->param = $vars;
				//goi ham sinh anh & luu ket qua
				require_once($imgCodePath.'rotate_image.php');
				server_rotate_image($s);
				$obj = $s->msg;
			}
			$result  = @unserialize($obj);
			if($result && $result['err'] == 0){
				$err = $result['msg'];
				return true; //success upload image & resize on server
			}else{
				$err = "Lỗi khi upload ảnh. Chi tiết: ".$obj;
			}
		}
		return false;
	}
	
	static function resizeUpload($sourceName = '', $desName = '', $desWidth=0, $desHeight=0, $water_mask = false, $mode=FTP_BINARY){
		$tmp = ROOT_PATH.IMAGE_PATH_STATIC.'tmp';
		$newFile = $tmp.'/'.self::getNameByTime($desName);
		self::CheckDir($tmp);
		if(self::uploadFile($sourceName, $newFile)){
			self::CheckDir(ROOT_PATH.IMAGE_PATH_STATIC.dirname($desName));
			$res = ImageHandler::genImageFromSource($newFile, ROOT_PATH.IMAGE_PATH_STATIC.$desName,$desWidth,$desHeight,$water_mask);
			if(IS_UPLOAD_IMAGE_SERVER && $res){
				return self::uploadFileFTP(ROOT_PATH.IMAGE_PATH_STATIC.$desName,$desName,FTP_BINARY);
			}elseif(DEBUG && !$res){
				echo 'Error: Can not create image from source '.ROOT_PATH.IMAGE_PATH_STATIC.$desName.'<br />';
			}
			@unlink($newFile);
			return $res;
		}elseif(DEBUG){
			echo 'Error: Upload file<br />';
		}
		return false;
	}

	static function upload($sourceName = '', $desName = '', $copy = false, $mode=FTP_BINARY){
		if(IS_UPLOAD_IMAGE_SERVER){
			return self::uploadFileFTP($sourceName,$desName, FTP_BINARY);
		}
		return self::uploadFile($sourceName, ROOT_PATH.IMAGE_PATH.$desName, $copy);
	}
	
	static function deleteByTime($fname = '', $time = 0, $sizeKey = ''){
		$ok = false;
		if($fname != '' && $time > 0 && $sizeKey != '' && isset(CGlobal::$imageSize[$sizeKey])){
			$file = DB::fetch("SELECT * FROM ".T_FILE_UPLOAD." WHERE file = '$fname' AND time = $time");
			if($file){
				$url  = ImageUrl::getImageServerUrl() . IMAGE_CODE_DIR . 'delete_image.php';
				$vars = array(
					'file_name' =>	$fname,
					'time' => $time,
					'type' => $sizeKey
				);
				if(IS_UPLOAD_IMAGE_SERVER){
					$curl = new CURL();
					$obj  = $curl->post($url, $vars);
				}else{
					//sinh anh tren cung 1 server
					$vars['from_client'] = 1;
					$imgCodePath = ROOT_PATH.IMAGE_PATH_STATIC.IMAGE_CODE_DIR;
					require_once($imgCodePath.'config.inc.php');
					$s = new SuperImageServer();
					$s->param = $vars;
					//goi ham sinh anh & luu ket qua
					require_once($imgCodePath.'delete_image.php');
					server_delete_image($s);
					$obj = $s->msg;
				}
				$result  = @unserialize($obj);
				if($result && $result['err'] == 0){
					//xoa khoi DB
					DB::delete(T_FILE_UPLOAD, "file = '$fname' AND time = $time");
					$ok = true;
				}
			}
		}
		return $ok;
	}
	
	static function delete($sourceName = '', $time = 0){
		if(IS_UPLOAD_IMAGE_SERVER){
			return self::deleteFileFTP($sourceName);
		}
		return self::deleteFile(ROOT_PATH.IMAGE_PATH.$sourceName);
	}

	static function uploadFile($sourceName = '', $desName = '', $copy = false){
		preg_match("#(.*)\/#", $desName, $dir);
		if(isset($dir[1]) && self::CheckDir($dir[1])){
			if($copy){
				return copy($sourceName, $desName);
			}
			return move_uploaded_file($sourceName, $desName);
		}
		return false;
	}
	
	static function deleteFile($sourceName = ''){
		if(is_file($sourceName)){
			return unlink($sourceName);
		}
		return false;
	}
	
	static function uploadFileFTP($sourceName = '', $desName = '',$mode=FTP_BINARY){
		if(($sourceName != '') && ($desName != '') && self::ftp_image_connect()){
			if(FileHandler::ftp_check_dir(dirname($desName))){
				if(@ftp_put(CGlobal::$ftp_image_connect_id, $desName, $sourceName, $mode)){
					@ftp_chmod ( CGlobal::$ftp_image_connect_id, 0777, $desName );
					return true;
				}
			}
		}
		return false;
	}
	
	static function deleteFileFTP($sourceFileName = ''){
		if(($sourceFileName != '') && self::ftp_image_connect()){
			if(@ftp_delete(CGlobal::$ftp_image_connect_id, IMAGE_SERVER_TEMP_PATH.$sourceFileName)){
				return true;
			}
		}
		return false;
	}
	
	static function ftp_image_connect(){
		if(!CGlobal::$ftp_image_connect_id){
			CGlobal::$ftp_image_connect_id = ftp_connect(FTP_IMAGE_SERVER);

			if(CGlobal::$ftp_image_connect_id){
				// Login to FTP Server
				$login_result= ftp_login(CGlobal::$ftp_image_connect_id, FTP_IMAGE_USER, FTP_IMAGE_PASSWORD);
				if($login_result){
					// turn passive mode on
					ftp_pasv(CGlobal::$ftp_image_connect_id, true);
					return true;
				}
				return false;
			}
			return false;
		}
		return true;
	}

	static function ftp_image_close(){
		if(CGlobal::$ftp_image_connect_id){
			ftp_close(CGlobal::$ftp_image_connect_id);
			CGlobal::$ftp_image_connect_id=false;
		}
	}

	static function ftp_check_dir($remote_dir_path,$mkdir=true){
		$ret = true;
		if(self::ftp_image_connect()){
			if($remote_dir_path=='')	return true;

			$dir=explode("/", $remote_dir_path);
			$remote_dir_path="";

			for ($i=0;$i<count($dir);$i++){
				if($dir[$i]!=''){
					$remote_dir_path.="/".$dir[$i];
					if(!@ftp_chdir(CGlobal::$ftp_image_connect_id,$remote_dir_path)){
						if($mkdir){
							@ftp_chdir(CGlobal::$ftp_image_connect_id,"/");

							if(!@ftp_mkdir(CGlobal::$ftp_image_connect_id,$remote_dir_path)){
								$ret=false;
								break;
							}
						}
						else{
							$ret=false;
							break;
						}
					}
				}
			}
			@ftp_chdir(CGlobal::$ftp_image_connect_id,"/");
		}
		else{
			$ret=false;
		}
		return $ret;
	}
	
	static function getExtension($file = '', $default = 'jpg') {
		if(preg_match("#.*\.(.*)#", $file, $tail) > 0){
			$tail = array_pop($tail);
		}else{
			$tail = $default;
		}
		return $tail;
	}
	
	static function getNameByTime($filename='', &$tail = '', $tit = ''){
		if($tail == ''){
			$tail = self::getExtension($filename);
		}
		if($tail != ''){
			if($tit == ''){
				$tmp = explode('.', $filename);
				$tit = $tmp[0];
			}
			$body = StringLib::safe_title($tit);
			if(strlen($body) > 116){
				$body = substr($body, 0, 116);
			}
			$body = str_replace('-', '_', $body) . '_' . time();
		}else{
			$body = str_replace(' ','',microtime());
		}
		return $body.'.'.$tail;
	}
	
	static function createdDirByTime($time = 0){
		$time = $time > 0 ? $time : TIME_NOW;
		return FunctionLib::dateFormat($time, 'Y/m/d/');
	}
	
	static function CheckDir($pDir){
		if (is_dir($pDir))
		return true;
		if (!@mkdir($pDir,0777,true)){
			return false;
		}
		self::chmod_dir($pDir,0777);
		return true;
	}

	static function chmod_dir($dir,$mod=0777){
		$parent_dir=dirname(str_replace(ROOT_PATH,'',$dir));
		if($parent_dir!='' && $parent_dir!='.'){
			//echo $parent_dir.'/<br />';
			@chmod($dir,$mod);
			self::chmod_dir($parent_dir,$mod);
		}
		return true;
	}
	
	static function getDimensionFile($size = 0){
		$kb = 1024;
		$mb = 1024*$kb;
		if($size >= $mb){
			return number_format(round($size/$mb, 10), 2)." MB";
		}elseif($size >= $kb){
			return number_format(round($size/$kb, 10), 2)." KB";
		}
		return $size.' Bytes';
	}
    static function curl_get_file_size( $url ) {
        // Assume failure.
        $result = -1;

        $curl = curl_init( $url );

        // Issue a HEAD request and follow any redirects.
        curl_setopt( $curl, CURLOPT_NOBODY, true );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
//        curl_setopt( $curl, CURLOPT_USERAGENT, get_user_agent_string() );

        $data = curl_exec( $curl );
        curl_close( $curl );

        if( $data ) {
            $content_length = "unknown";
            $status = "unknown";

            if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
                $status = (int)$matches[1];
            }

            if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
                $content_length = (int)$matches[1];
            }

            // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
            if( $status == 200 || ($status > 300 && $status <= 308) ) {
                $result = $content_length;
            }
        }

        return $result;
    }
}
