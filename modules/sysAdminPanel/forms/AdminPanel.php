<?php
class AdminPanelForm extends Form {
    var $try_export; //thu tinh nang xuat excel
    function __construct(){
		parent::__construct();
		$this->try_export = ConfigSite::getConfigFromDB('try_export',0,false,'module_configs');
		if($this->try_export == 1){
			$this->link_js('js/admin/admin_exporter.js');
		}
	}
    
    function draw(){
		global $display;
		
		//FileHandler::rotateImageOnServer('2002_12_08_12_00_00_5_1466675800.jpg', '1466675800', 'gallery', 90, $err);
		//echo "<img src='".Gallery::getImageGallery('2002_12_08_12_00_00_5_1466675800.jpg', '1466675800', 350)."' />";
		$cUser = User::$current->data;

		$display->add('cUser', $cUser);
		$display->add('online', $this->userOnline());

        $this->layout->init(array('style' => 'html', 'form' => false));
		
		$html = $this->layout->genHeaderAuto(array('title' => 'Xin chào, '.$cUser['username'].'!'));
		$html.= '<div class="row clearfix">';
		$html.= $this->layout->genPanelAuto(array(
			'title' => 'Thông tin đăng nhập',
			'color_head' => 'cyan',
			'size' => array('lg' => 6, 'md' => 6, 'sm' => 12, 'xs' => 12),
			'html' => $display->output('loginInfo', true)
		));
		$html.= $this->layout->genPanelAuto(array(
			'title' => 'Người dùng đang Online',
			'color_head' => 'green',
			'size' => array('lg' => 6, 'md' => 6, 'sm' => 12, 'xs' => 12),
			'html' => $display->output('online', true)
		));
		$html.= '</div>';
		if($this->try_export){
			$uploadForm = new Form('uploadForm');
			$uploadForm->layout->init(array(
				'style' => 'edit',
				'method' => 'POST'
			));
			$uploadForm->layout->addGroup('main', array('title' => 'TEST UPLOAD IMAGES', 'color_head' => 'pink'));
			$uploadForm->layout->addItem('test_upload', array(
				'type'  => "file"
			), 'main');
			
			$html.= $uploadForm->layout->genFormAuto($uploadForm, array(
				'html_button_cancel' => '&nbsp;',
				'html_button_submit' => $uploadForm->layout->genButtonAuto(array(
					'title' => 'Upload',
					'style' => 0,
					'color' => 'success',
					'size' => 1
				))
			), true);
		}
		$this->layout->genFormAuto($this, array('html' => $html));
    }
	
	function userOnline(){
		$check_online_time = TIME_NOW - CGlobal::$checkOnlineTime;
		$user = array();
		$res = DB::query("SELECT id, username, fullname, email, mobile_phone, gender, last_action
						  FROM ". T_USERS ."
						  WHERE status = 1 AND last_action > $check_online_time
						  ORDER BY last_action DESC");
		while($r = @mysql_fetch_assoc($res)){
			$user[$r['id']] = array(
				'id'	=> $r['id'],
				'name' 	=> $r['username'],
				'fname' => $r['fullname'],
				'email'	=> $r['email'],
				'phone'	=> $r['mobile_phone'],
				'gender'=> ($r['gender'] == 1 ? 'Nam' : 'Nữ'),
				'time'  => $r['last_action'],
				'roles' => ''
			);
		}
		if(!empty($user)){
			$ids = implode(',', array_keys($user));
			$res = DB::query("SELECT * FROM ".T_USER_ROLES." WHERE uid IN ($ids) ORDER BY uid");
			while($r = @mysql_fetch_assoc($res)){
				if(isset(CGlobal::$permission_group[$r['rid']])){
					$user[$r['uid']]['roles'] .= CGlobal::$permission_group[$r['rid']]['title'].', ';
				}
			}
			foreach($user as $k => $v){
				$user[$k]['roles'] = substr($v['roles'], 0, -2);
			}
		}
		return $user;
	}

    function on_submit(){
        echo '<h1 align="center">Demo upload file</h1>';

        $time = TIME_NOW;

        if(isset($_FILES['test_upload'])){
            $file = $_FILES['test_upload'];
            $folderUpload = GALLERY_FOLDER;
            $sizeKey = 'gallery';
            $path = pathinfo($file['name']);
            $fileName = $path['filename'];
			
			$sever = 1;
			if($sever){
				//Demo resize on server
				$fileUploadResult = FileHandler::resizeImageOnServer($file, $fileName, $time, $sizeKey, $folderUpload, $err);
				if($fileUploadResult){
					foreach(CGlobal::$imageSize[$sizeKey] as $k => $v){
						echo '<img src="'.Gallery::getImageGallery($err, $time, $k).'" style="float:left;margin-left:10px" />';
					}
				}elseif($err != ''){
					echo $err;
				}
			}else{
				$water_mask = 1;
				//Demo resize on client
				$fname = FileHandler::getNameByTime($file['name']);
				if(FileHandler::resizeUpload($file['tmp_name'], SITEINFO_FOLDER.$fname, 640, 0, $water_mask == 1)){
					echo '<img src="'.ImageUrl::getSiteFavicon($fname).'" style="float:left" />';
				}
			}
        }
        exit();
    }
}
