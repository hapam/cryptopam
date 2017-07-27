<?php
class CategoryForm extends Form{
	private $size, $cmd = 'category', $table = T_CATEGORY;
    function __construct(){
		$this->link_js_me('admin_category.js', __FILE__);
		$this->size = ImageUrl::getSize(CATEGORY_KEY, 'min');
    }

    function draw(){
        global $display;

        $title = Url::getParam('title');
        $cond[] = 'status IN (0,1)';
        if($title != ''){
            $cond[] = "title LIKE '%$title%'";
        }

        $search_value = FunctionLib::addCondition($cond);
		$search_value = ($search_value != '') ? ' WHERE '.$search_value : '';

		$sql= 'SELECT * FROM ' . $this->table . $search_value .' ORDER BY type, weight, safe_title';
		$re = DB::query($sql);
		$items = array();
		if($re){
			while ($row=mysql_fetch_assoc($re)){
				$row['image'] = Category::getCategoryImage($row['image'], $row['created'], $this->size);
				if($row['parent_id'] == 0){
					$items[$row['id']]['data'] = $row;
				}else{
					$items[$row['parent_id']]['items'][$row['id']] = $row;
				}
			}
			if(!empty($items)){
				foreach($items as $p => $cat){
					if(!isset($cat['data'])){
						foreach($items as $p1 => $cat1){
							if(isset($cat1['data']) && !empty($cat1['items']) && isset($cat1['items'][$p])){
								$items[$p1]['items'][$p]['extra'] = $items[$p]['items'];
							}
						}
						unset($items[$p]);
					}
				}
			}
		}

	    $msg = $this->showFormErrorMessages(1);
		if($msg == ''){
			$msg = $this->showFormSuccesMessages(1);
		}
		$display->add('msg',$msg);

        $display->add('title',$title);
        $display->add('items',$items);
		$display->add('type', CGlobal::get('categoryType'));
		$display->add('formName', $this->name);
        
		$display->add('hover',FunctionLib::mouse_hover('#E2F1DF',true));
		
		//link
        $display->add('delLink', Url::buildAdminURL('admin',array('cmd'=>'category','action'=>'delete')));
        $display->add('addUrl', Url::buildAdminURL('admin',array('cmd' => 'category', 'action' => 'add')));
		$display->add('editLink', Url::buildAdminURL('admin',array('cmd' => 'category', 'action' => 'edit')));
		
		//permission
        $display->add('add_cat', User::user_access("add category"));
		$display->add('edit_cat', User::user_access("edit category"));
		$display->add('delete_cat', User::user_access("delete category"));

        $this->beginForm(false, "GET");
        $display->output("list");
        $this->endForm();
    }

    function on_submit(){
		$pids = Url::getParam('selected_ids', array());
        if(!empty($pids)){
			$pids = implode(',', $pids);
			$res = DB::query("SELECT id FROM ".$this->table." WHERE parent_id IN ($pids)");
			$ids = '';
			while($row = @mysql_fetch_assoc($res)){
				$ids .= $row['id'].',';
			}
			$ids = ($ids != '') ? substr($ids, 0, -1) : '';
			if($ids != ''){
				DB::update($this->table, array('status' => -1), "id IN($ids) OR parent_id IN($ids)");
			}
			DB::update($this->table, array('status' => -1), "id IN ($pids)");
			
			Category::delCache('', true);
			
			$this->setFormSucces('', "Xóa thành công! Danh mục có mã ($pids) đã bị xóa");
		}else{
			$this->setFormError('', "Bạn chưa chọn danh mục cần xóa");
		}
    }
}