<?php

class EditCategoryForm extends Form {
    private $category, $pid = 0, $cmd = 'category', $action, $table = T_CATEGORY, $id, $size;
    function __construct() {
        $this->id = Url::getParamInt('id', 0);
        $this->action = Url::getParamAdmin('action');
        $this->pid = Url::getParamInt('pid', 0);

        switch ($this->action) {
            case 'edit':
                if ($this->id > 0) {
                    if ($this->category = DB::select($this->table, 'id=' . $this->id)) {
                        $this->category = $this->category[$this->id];
                    }
                }
                if (!$this->category) {
                    Url::redirect('admin', array('cmd' => $this->cmd));
                }

                break;
            case 'delete':
                $this->deleteCategory($this->id);
                break;
        }
        $this->link_js_me('admin_category.js', __FILE__);
        
        $this->size = ImageUrl::getSize(CATEGORY_KEY, 'min');
    }

    function draw() {
        global $display;

        $msg = $this->showFormErrorMessages(1);

        $parent = Url::getParamInt('parent_id', ($this->action == 'edit' ? $this->category['parent_id'] : $this->pid));
        $parent_info = array();
        $default_type = !empty($this->category) ? $this->category['type'] : 0;
        if ($parent > 0) {
            $parent_info = DB::fetch("SELECT * FROM " . $this->table . " WHERE id = $parent");
            if (!empty($parent_info)) {
                $default_type = $parent_info['type'];
            }
        }
        $type_id = Url::getParamInt('type', $default_type);
        $parentIds = Category::optCategory($parent, $type_id, $defCatReturn);
        if (stripos($msg, 'Cấp của danh mục tối đa là 3') !== 0) {
            if ($this->action == 'edit') {
                $parent = $this->category['parent_id'];
            } else {
                $parent = $this->pid;
            }
        }
        $defType = -1;
        if (!empty($defCatReturn)) {
            $defType = $defCatReturn['type'];
        }

        $status = array('' => ' -- Chọn -- ', '1' => 'Hiện', '0' => 'Ẩn');
        $type = array('-1' => ' -- Chọn -- ') + CGlobal::get('categoryType', array());

        if ($msg == '') {
            $msg = Url::getParam('msg');
            if ($msg != '') {
                $this->setFormSucces('', 'Đã thêm danh mục thành công!!!');
                $msg = $this->showFormSuccesMessages(1);
            }
        }
        $img = Url::getParam('path', $this->category['image']);
        $display->add('msg', $msg);
        $display->add('id', $this->id);
        $display->add('active', Url::getParam('active', $this->category['special']));
        $display->add('title', Url::getParam('title', $this->category['title']));
        $display->add('weight', Url::getParamInt('weight', $this->category['weight']));
        $display->add('status', FunctionLib::getOption($status, Url::getParamInt('status', $this->category ? $this->category['status'] : 1)));
        $display->add('type', FunctionLib::getOption($type, $type_id));
        $display->add('parent_id', $parentIds);
        $display->add('delLink', Url::buildAdminURL('admin', array('cmd' => 'category', 'action' => 'delete')));

        $display->add('path', $img);
        $display->add('image', ($img != '') ? Category::getCategoryImage($img, $this->category['created'], $this->size) : '');

        $display->add('type_id', $type_id);

        $this->beginForm(true);
        $display->output("edit");
        $this->endForm();
    }

    function on_submit() {
        $title = Url::getParam('title', '');
        $active = Url::getParamInt('active');
        $parent_id = Url::getParamInt('parent_id');
        $status = Url::getParamInt('status', 0);
        $type = Url::getParamInt('type', 0);
        $weight = Url::getParamInt('weight', 1000);
        $old_img = Url::getParam('old_file', '');

        if($title != ''){
			$this->setFormError('title','Chưa nhập tên danh mục');
		}

        if ($this->errNum == 0) {
            if($this->action == 'edit' && $parent_id == $this->category['id']){
                $this->setFormError('parent_id', 'Vui lòng chọn lại danh mục cha');
            }
            if ($this->errNum == 0) {
                if ($parent_id > 0) {
                    //dam bao weight cua danh muc con luon be hon danh muc cha
                    $parent = DB::fetch("SELECT weight, type FROM " . $this->table . " WHERE id = $parent_id");
                    if ($weight <= $parent['weight']) {
                        $weight = $parent['weight'] + ($weight < 1 ? 1 : $weight);
                    }
                    $type = $parent['type'];
                }
    
                $safe_title = StringLib::safe_title($title);
                $new_row = array(
                    'title' => $title,
                    'safe_title' => strtolower($safe_title),
                    'parent_id' => $parent_id,
                    'status' => $status,
                    'type' => $type,
                    'weight' => $weight,
                    'special' => $active
                );
    
                if ($this->action == 'edit') {
                    if ($parent_id > 0) {
                        $nowSub = $this->checkSubFromID($this->category['id']);
                        if ($nowSub >= 3) {
                            $this->setFormError('parent_id', 'Vui lòng chọn lại danh mục cha. Cấp của danh mục tối đa là 3');
                        } else {
                            $parentSub = $this->checkSubFromID($parent_id, $is_root);
                            if (($is_root && $nowSub >= 3) || (!$is_root && $parentSub > 0 && $nowSub > 0)) {
                                $this->setFormError('parent_id', 'Vui lòng chọn lại danh mục cha. Cấp của danh mục tối đa là 3.');
                            }
                        }
                    }
                }
                if ($this->errNum == 0) {
                    if ($this->action == 'edit') {
                        $time = $this->category['created'];
                    }else{
                        $time = TIME_NOW;
                    }
                
                    $sizeKey = CATEGORY_KEY;
                    $folderUpload = CATEGORY_FOLDER;
                    $err = '';
                    
                    //upload image
                    $fileName = $new_row['title'];
                    $file = $_FILES['path'];
                    $fileUploadResult = FileHandler::resizeImageOnServer($file, $fileName, $time, $sizeKey, $folderUpload, $err, $old_img);
                    if ($fileUploadResult) {
                        $new_row['image'] = $err;
                    } elseif ($err != '') {
                        $this->setFormError('', $err);
                    }
                
                    if ($this->action == 'edit') {
                        DB::update($this->table, $new_row, 'id=' . $this->category['id']);
    
                        //tien hanh cap nhat lai cho chuan
                        $ids = '';
                        $res = DB::query("SELECT id FROM " . $this->table . " WHERE parent_id = " . $this->category['id']);
                        while ($row = @mysql_fetch_assoc($res)) {
                            $ids .= $row['id'] . ',';
                        }
                        $ids = ($ids != '') ? substr($ids, 0, -1) : '';
                        if ($ids != '') {
                            //neu danh muc cha bi an hoac bi xoa thi cac danh muc con cung bi tac dong theo
                            $sql = "status=$status";
                            //cac danh muc con phai cung loai voi danh muc cha
                            $sql .= ", type=$type";
                            //tinh toan lai weight cua danh muc con neu danh muc cha bi sap xep lui di
                            if ($weight > $this->category['weight']) {
                                $w = $weight - $this->category['weight'];
                                $sql .= ", weight=weight+$w";
                            }
                            //cap nhat danh muc cap 2 & cap 3
                            DB::query("UPDATE " . $this->table . " SET $sql WHERE status != -1 AND (id IN ($ids) OR parent_id IN ($ids))");
                        }
                    } else {
                        $new_row['created'] = $time;
                        $this->category['id'] = DB::insert($this->table, $new_row);
                    }
                    Category::delCache($new_row['type']);
                    //redirect
                    if ($this->pid > 0) {
                        Url::redirect('admin', array('cmd' => 'category', 'action' => 'add', 'pid' => $this->pid, 'msg' => 'success'));
                    } else {
                        Url::redirect('admin', array('cmd' => 'category'));
                    }
                }
            }
        }
    }

    function checkSubFromID($id = 0, &$is_root = false) {
        if ($id > 0) {
            $check = DB::fetch("SELECT parent_id, status FROM " . $this->table . " WHERE id = $id");
            if ($check && $check['status'] >= 0) {
                $is_root = ($check['parent_id'] == 0);
                $sub = DB::fetch_all("SELECT id FROM " . $this->table . " WHERE status IN (0,1) AND parent_id = $id");
                if ($sub) {
                    $ids = implode(',', array_keys($sub));
                    $c = DB::count($this->table, "status IN (0,1) AND parent_id in($ids)");
                    if ($c > 0) {
                        return 3;
                    }
                    return 2;
                } else {
                    return 0;
                }
            }
            return 10;
        }
        return 0;
    }

    function deleteCategory($id = 0) {
        if ($id > 0) {
            $ids = '';
            $type = '';
            $res = DB::query("SELECT id, type FROM " . $this->table . " WHERE parent_id = $id");
            while ($row = @mysql_fetch_assoc($res)) {
                $ids .= $row['id'] . ',';
                $type = $row['type'];
            }
            $ids = ($ids != '') ? substr($ids, 0, -1) : '';
            if ($ids != '') {
                DB::update($this->table, array('status' => -1), "id IN($ids) OR parent_id IN($ids)");
            }
            DB::update($this->table, array('status' => -1), "id = $id");
            
            Category::delCache($type);
            Url::redirect('admin', array('cmd' => 'category'));
        }
    }

}