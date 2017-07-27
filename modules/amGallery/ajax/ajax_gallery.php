<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_gallery {
    function playme(){
        $code = Url::getParam('code');
        if($code == 'multi-upload'){
            $action = Url::getParam('action', '');
			if($action == 'insert'){
				$this->multiuploadInsert();//chen anh vao bai viet su dung uploadtify
			}else{
				$this->multiupload(); //upload su dung uploadtify trong gallery
			}
        }else if(User::user_access('use gallery')){
			switch( $code ){
				case 'upload':
					$this->upload(); //upload 1 anh qua ajax
				break;
				case 'cat-refresh':
					$this->catRefresh();
				break;
				case 'category':
					$this->category();
				break;
				case 'list-category':
					$this->listCategory();
				break;
				case 'remove-cat':
					$this->removeCategory();
				break;
				case 'remove-item':
					$this->removeImage();
				break;
				case 'cover':
					$this->setCover();
				break;
				case 'load-images':
					$this->getImages();
				break;
				case 'change-pos':
					$this->changePosition();
				break;
				default: $this->home();
			}
		}else{
			FunctionLib::JsonErr('access_dined', false, true);
		}
    }

	function multiuploadInsert(){
		$size = Url::getParamInt('size', 0);
		$uid = Url::getParamInt('uid');
		$user = User::getUser($uid);
		if($user && $user['id'] > 0){
			if (!empty($_FILES) && !empty($_FILES['Filedata'])) {
				$basename = basename($_FILES['Filedata']['name']);
				// Kiem tra duoi anh co hop le khong
				if (!FileHandler::isImageFile($basename)) {
					echo FunctionLib::JsonErr('Invalid extension');
				}else{
					$file = $_FILES['Filedata'];
					if($file['size'] > 0 && $file['size'] < CGlobal::$max_upload_size){
						$catID = Gallery::$defCatID;// thu muc mac dinh
						$data = array(
							'cat_id'=>  $catID, 
							'title' =>  '',
							'type'  =>  $file['type'],
							'image' =>  '',
							'created' => TIME_NOW,
							'uid' => $user['id'],
							'uname' => $user['username'],
							'sort' => Gallery::getSortInsert()
						);
						
						$time = TIME_NOW;
						//upload image to server & resize
						$folderUpload = GALLERY_FOLDER;
						$sizeKey = 'gallery';
						$path = pathinfo($file['name']);
						$fileName = $path['filename'];
						$fileUploadResult = FileHandler::resizeImageOnServer($file, $fileName, $time, $sizeKey, $folderUpload, $err);
						if($fileUploadResult){
							$size = $size > 0 ? $size : ImageUrl::getSize('gallery','max');
							$data['image'] = $err;
							$data['title'] = $fileName;
							
							DB::insert(T_GALLERY, $data);
							//update so luong
							$count = DB::count(T_GALLERY, "cat_id = $catID");
							DB::query("UPDATE ". T_GALLERY_CATS ." SET total=$count WHERE id=".$catID);
							
							$result = Gallery::getImageGallery($data['image'], $data['created'], $size > 0 ? $size : 640);
							FunctionLib::JsonSuccess($result, array(), true);
						}elseif($err != ''){
							FunctionLib::JsonErr('Create Image Error', array(), true);
						}
					}
					FunctionLib::JsonErr('Invalid Size', array(), true);
				}
			}
			echo FunctionLib::JsonErr('File Data Error', array(), true);
		}
		FunctionLib::JsonErr('Permission Denied', array(), true);
	}

    function multiupload(){
        $uid = Url::getParamInt('uid');
        $user = User::getUser($uid);
        if($user && $user['id'] > 0){
            if (!empty($_FILES) && !empty($_FILES['Filedata'])) {
                $basename = basename($_FILES['Filedata']['name']);
                // Kiem tra duoi anh co hop le khong
                if (!FileHandler::isImageFile($basename)) {
                    echo FunctionLib::JsonErr('Invalid extension');
                }else{
                    $file = $_FILES['Filedata'];
                    if($file['size'] > 0 && $file['size'] < CGlobal::$max_upload_size){
                        $catID = Url::getParamInt('cat', Gallery::$defCatID);
						$sort = Url::getParamInt('sort', Gallery::getSortInsert());
                        $data = array(
                            'cat_id'=>  $catID,
                            'title' =>  '',
                            'type'  =>  $file['type'],
                            'image' =>  '',
                            'created' => TIME_NOW,
                            'uid' => $user['id'],
                            'uname' => $user['username'],
							'sort'=>$sort
                        );

                        $time = TIME_NOW;
                        //upload image to server & resize
                        $folderUpload = GALLERY_FOLDER;
                        $sizeKey = GALLERY_KEY;
                        $path = pathinfo($file['name']);
                        $fileName = $path['filename'];
                        $fileUploadResult = FileHandler::resizeImageOnServer($file, $fileName, $time, $sizeKey, $folderUpload, $err);
                        if($fileUploadResult){
                            $data['image'] = $err;
                            $data['title'] = $fileName;

                            $data['id'] = DB::insert(T_GALLERY, $data);
                            //update so luong
							$count = DB::count(T_GALLERY, "cat_id = $catID");
							DB::query("UPDATE ". T_GALLERY_CATS ." SET total=$count WHERE id=".$catID);

                            $data['img'] = !empty($file) ? $data['image'] : '';
                            $data['image'] = Gallery::getImageGallery($data['img'], $time, ImageUrl::getSize(GALLERY_KEY, 'max'));
                            $data['image_sm'] = Gallery::getImageGallery($data['img'], $time, ImageUrl::getSize(GALLERY_KEY, 'min'));

                            FunctionLib::JsonSuccess('ok', array('data' => $data), true);
                        }elseif($err != ''){
                            FunctionLib::JsonErr('Create Image Error', array(), true);
                        }
                    }
					FunctionLib::JsonErr('Invalid Size', array(), true);
                }
            }
			FunctionLib::JsonErr('File Data Error', array(), true);
        }
		FunctionLib::JsonErr('Permission Denied', array(), true);
    }

    function upload(){
        $action = Url::getParam('myaction', 'add');
        $editId = Url::getParamInt('id', 0);
        $catID = Url::getParamInt('cat', Gallery::$defCatID);
		$title = Url::getParam('title', '');
        $sort = Url::getParamInt('sort', Gallery::getSortInsert());
        $old_img = Url::getParam('old_image', '');
        $curUser = User::$current->data;
		
		$max_item = Gallery::$maxItemPerDirect;
		
		$msg = '';
		
		$cat = DB::fetch("SELECT * FROM ".T_GALLERY_CATS." WHERE id = $catID");
		if($cat){
			if($cat['total'] > $max_item){
				$msg = 'Thư mục chứa ảnh đã đầy! Vui lòng tạo thư mục khác.';
			}
		}else{
			$msg = 'Thư mục chứa ảnh không tồn tại';
		}

		if($msg == ''){
			$data = array(
				'cat_id'=>  $catID,
				'title' =>  $title,
				'image' =>  $old_img,
				'uid' => $curUser['id'],
				'uname' => $curUser['username'],
				'sort'=>$sort
			);
			$time = TIME_NOW;
			$item = array();
			if($editId > 0){
				$item = DB::fetch("SELECT * FROM ".T_GALLERY." WHERE id=$editId");
				$data['changed'] = TIME_NOW;
				$time = $item['created'];
			}
			$data['created'] = $time;
	
			//upload image to server & resize
			$file = array();
			if(isset($_FILES['image'])){
				$file = $_FILES['image'];
				$data['type'] = $file['type'];
				$folderUpload = GALLERY_FOLDER;
				$sizeKey = GALLERY_KEY;
				$path = pathinfo($file['name']);
				$fileName = $path['filename'];
				$fileUploadResult = FileHandler::resizeImageOnServer($file, $fileName, $time, $sizeKey, $folderUpload, $err, $old_img);
				if($fileUploadResult){
					$data['image'] = $err;
				}elseif($err != ''){
					$msg = $err;
				}
			}

			if($msg == '' && $data){
				if($action == 'add'){
					$editId = DB::insert(T_GALLERY, $data);
					//update so luong
					$count = DB::count(T_GALLERY, "cat_id = $catID");
					DB::query("UPDATE ". T_GALLERY_CATS ." SET total=$count WHERE id=".$catID);
				}else{
					if(!empty($item) && $item['cat_id'] != $catID){//update so luong
						//update so luong
						$count = DB::count(T_GALLERY, "cat_id = $catID");
						DB::query("UPDATE ". T_GALLERY_CATS ." SET total=$count WHERE id=".$catID);

						$count = DB::count(T_GALLERY, "cat_id = ".$item['cat_id']);
						DB::query("UPDATE ". T_GALLERY_CATS ." SET total=$count WHERE id=".$item['cat_id']);
					}
					if($data['title'] == ''){
						unset($data['title']);
					}
					DB::update(T_GALLERY, $data, "id=$editId");
				}
				$data['id'] = $editId;
				$data['img'] = !empty($file) ? $data['image'] : $item['image'];
				$data['image'] = Gallery::getImageGallery($data['img'], $time, 640);
				$data['image_sm'] = Gallery::getImageGallery($data['img'], $time, 150);
				
				FunctionLib::JsonSuccess($msg, array('data' => $data), true);
			}
		}
        FunctionLib::JsonErr($msg, array(), true);
    }

    function removeImage(){
        $id = Url::getParamInt('id', 0);
        $img= Url::getParam('img', '');
        $cat_id= Url::getParam('cat_id', 1);
        if($id > 0){
            $img = DB::fetch("SELECT * FROM ".T_GALLERY." WHERE id=$id");
            if($img){
				DB::query("UPDATE ". T_GALLERY_CATS ." SET total=total-1 WHERE id=".$cat_id);
				DB::delete_id(T_GALLERY,$id);
				FunctionLib::JsonSuccess('success', array(), true);
            }
        }
        FunctionLib::JsonSuccess('Không tìm được ảnh cần xóa', array(), true);
    }

	function setCover(){
        $id = Url::getParamInt('id', 0);
        if($id > 0){
            $img = DB::fetch("SELECT * FROM ".T_GALLERY." WHERE id=$id");
            if($img){
				DB::update(T_GALLERY, array('is_cover' => 0), "cat_id=".$img['cat_id']);
				DB::update(T_GALLERY, array('is_cover' => 1), "id=$id");
				
				$img['is_cover'] = 1;
				$img['img'] = $img['image'];
				$img['image'] = Gallery::getImageGallery($img['img'], $img['created'], 640);
				$img['image_sm'] = Gallery::getImageGallery($img['img'], $img['created'], 150);
				FunctionLib::JsonSuccess('success', array('data' => $img), true);
            }
        }
        FunctionLib::JsonSuccess('Không tìm được ảnh cần xóa', array(), true);
    }
    
    function category(){
        $curUser = User::$current->data;
        $action = Url::getParamAdmin('action','add');
        $data = array(
            'title' => trim(Url::getParam('title')),
            'description' => Url::getParam('description'),
            'uid' => $curUser['id'],
            'uname' => $curUser['username']
        );
        
		if($data['title'] != '') {
			$cate = DB::select(T_GALLERY_CATS, "title = '".$data['title']."'");
			if(empty($cate)) {
				if($action == 'add'){
					$data['created'] = TIME_NOW;
					$data['id'] = DB::insert(T_GALLERY_CATS, $data);
				}
				else if($action == 'edit'){
					$id  = Url::getParamInt('id', 0);
					if($id > 1){
						DB::update(T_GALLERY_CATS, $data, "id=$id");
						$data['id'] = $id;
					}
				}
				FunctionLib::JsonSuccess('success', array('data' => $data), true);
			}
			FunctionLib::JsonErr('Danh mục này đã tồn tại', array(), true);
		}
		FunctionLib::JsonErr('Bạn phải điền tên danh mục', array(), true);
	}
	
	function catRefresh(){
		$id  = Url::getParamInt('id', 0);
		if($id > 0){
			//update so luong
			$count = DB::count(T_GALLERY, "cat_id = $id");
			DB::query("UPDATE ". T_GALLERY_CATS ." SET total=$count WHERE id=".$id);
			
			FunctionLib::JsonSuccess('success', array(), true);
		}
		FunctionLib::JsonErr('Danh mục này đã tồn tại', array(), true);
	}
    
    function removeCategory(){
        $id  = Url::getParamInt('id', 0);
        if($id > 1){
            //move item to default folder
            DB::update(T_GALLERY, array('cat_id' => 1),"cat_id=$id");
            //update so luog danh muc
            $total = DB::fetch("SELECT count(*) as total FROM ".T_GALLERY." WHERE cat_id=1");
            DB::update(T_GALLERY_CATS,array('total' => $total['total']),"id=1");
            //del item
            DB::delete_id(T_GALLERY_CATS, $id);
            FunctionLib::JsonSuccess('success', array(), true);
        }
        FunctionLib::JsonErr($id == 1 ? 'Bạn không thể xóa thư mục mặc định' : 'Có lỗi xẩy ra', array(), true);
    }
    
    function getImages(){
        $id = Url::getParamInt('id');
        $img = array();
        $res = DB::query("SELECT * FROM " . T_GALLERY . " WHERE cat_id=$id ORDER BY sort ASC");
        
        while ($row = @mysql_fetch_assoc($res)) {
            $row['img'] = $row['image'];
            $row['image'] = Gallery::getImageGallery($row['img'], $row['created'], ImageUrl::getSize(GALLERY_KEY, 'max'));
            $row['image_sm'] = Gallery::getImageGallery($row['img'], $row['created'], ImageUrl::getSize(GALLERY_KEY, 'min'));

            $img[] = $row;
        }
        FunctionLib::JsonSuccess('success', array('data' => $img), true);
    }
    
    
    function listCategory(){
        $cat = array();
        $res = DB::query("SELECT id, title, description FROM ".T_GALLERY_CATS." ORDER BY title, created DESC");
        while($row = @mysql_fetch_assoc($res)){
            $cat[] = $row;
        }
        FunctionLib::JsonSuccess('success', array('data' => $cat), true);
    }
	
	function changePosition(){
        $id = Url::getParamInt('id', 0);
		$next = Url::getParamInt('next', 0);
		$type = Url::getParam('type', '');
        if($id > 0 && $next > 0 && $type != ''){
			$next = DB::fetch("SELECT * FROM ".T_GALLERY." WHERE id = $next");
			if($next){
				$new_pos = $type == 'left' ? ($next['sort'] - Gallery::$stepNext) : ($next['sort'] + Gallery::$stepNext);
				if(DB::update(T_GALLERY, array('sort' => $new_pos), "id=$id")){
					FunctionLib::JsonSuccess('success', array('data' => array()), true);
				}
			}
        }
        FunctionLib::JsonSuccess('Lỗi', array(), true);
    }

    
	function home(){ die("Nothing to do..."); }
}//class
