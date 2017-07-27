<?php
class ListPageAdminForm extends Form{
	private $table = T_PAGE, $page = 'page';
	public $perm, $link;

	function __construct(){
		parent::__construct();
		
		$this->perm = array(
            'add' => User::is_root(),
            'edit' => User::is_root(),
            'del' => User::is_root()
        );
		$this->link = array(
            'add' => Url::buildAdminURL($this->page, array('cmd' => 'add')),
            'edit' => Url::buildAdminURL($this->page, array('cmd' => 'edit')),
            'del' => Url::buildAdminURL($this->page, array('cmd' => 'delete')),
			'copy' => Url::buildAdminURL($this->page, array('cmd' => 'copy')),
			'cache' => Url::buildAdminURL($this->page, array('cmd' => 'delete_all_cache'))
        );
	}
	
	function draw(){
		global $display;

		$name		= Url::getParam('name');
		$order_by	= Url::getParam('order_by','id');
		$order_dir	= Url::getParam('order_dir','DESC');

		$cores = array();
		if($name!=''){
			$cond = 'name LIKE "%'.$name.'%"';
		}else{
			$coreIds = implode(',', array_values(CGlobal::$corePages));
			$cond = 'id NOT IN ('.$coreIds.')';
			$res = DB::query('SELECT  id ,name,title, description, layout FROM '.T_PAGE.' WHERE id IN ('.$coreIds.')');
			while($row = @mysql_fetch_assoc($res)){
				$row['href'] = ($row['name'] == 'edit_page') ? 'javascript:void(0)' : Url::buildAdminURL('edit_page',array('id'=>$row['id']));
				$row['url'] = '<a href="'.$row['href'].'" title="Bố cục trang" target="_blank">'.$row['name'].'</a>';
				$row['layout'] .= '<a href="'.$row['href'].'" class="pull-right" target="_blank" title="Xem bố cục trang"><i class="material-icons">web</i></a>';
				$cores[$row['id']] = $row;
			}
		}
		
		$items = array();
		$paging = '';
		$item_per_page = 20;

		$sql='SELECT  id ,name,title, description, layout, themes, keyword FROM '.T_PAGE.' WHERE '.$cond.' ORDER BY '.$order_by.' '.$order_dir;
		$re = Pagging::pager_query($sql,$item_per_page);
		if($re){
			while ($row=mysql_fetch_assoc($re)){
				$row['href'] = Url::buildAdminURL('edit_page',array('id'=>$row['id']));
				$row['url'] = '<a href="'.$row['href'].'" title="Bố cục trang" target="_blank">'.$row['name'].'</a>';
				$row['view'] = Url::buildURL($row['name']);
				$row['copy'] = $this->link['copy'].'?id='.$row['id'];
				$row['key_icon']  = array(
					'icon' => 'check_circle',
					'color'=> !empty($row['description']) ? '' : 'grey'
				);
				$row['des_icon']  = array(
					'icon' => 'check_circle',
					'color'=> !empty($row['keyword']) ? '' : 'grey'
				);
				$row['themes'] = !empty($row['themes']) ? $row['themes'] : '---';
				$row['layout'] .= '<a href="'.$row['href'].'" class="pull-right" target="_blank" title="Xem bố cục trang"><i class="material-icons">web</i></a>';
				
				if(isset(CGlobal::$noDeletePages[$row['name']]) || isset(CGlobal::$corePages[$row['name']])){
					$row['btn-del'] = array('hide' => true);
					$row['btn-del-check'] = array('hide' => true);
				}
				if(isset(CGlobal::$corePages[$row['name']])){
					$row['btn-edit'] = array('hide' => true);
					unset($row['copy']);
				}
				
				$items[$row['id']] = $row;
			}
			$paging = Pagging::getPager(3, false, 'page_no', true);
		}
		
		PAGE::autoList($this, array(
			'items' => $items,
			'cores' => $cores,
			'search'=> $name != '',
			'pagging' => array(
				'start_page' => (Pagging::$page-1)*$item_per_page,
				'total_item' => Pagging::$totalResult,
				'total_page' => Pagging::$totalPage,
				'pager'	=> $paging
			)
		));
	}
	
	function on_submit(){
		$selected_ids = Url::getParam('selected_ids');
		if(!empty($selected_ids)){
			$ids = implode(',',$selected_ids);
			if(!empty($ids)){
				Layout::update_page($ids);
				DB::delete(T_BLOCK, 'page_id IN('.$ids.')'); 
				DB::delete(T_PAGE, 'id IN('.$ids.')');

				//xoa thu muc cached
				FunctionLib::empty_all_dir(DIR_CACHE,true,true);
				require(ROOT_PATH.'tools/delcache.php');
			}
			Url::redirect_current();
		}
		$this->setFormError('', 'Chưa chọn trang cần xóa');
	}
}
