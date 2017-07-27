<?php
class AdminLangForm extends Form{
    private $cmd = 'lang', $table = T_LANG;
	public $perm, $link;
    function __construct(){
		parent::__construct();
		$this->perm = array(
			'add'  => User::user_access("add lang"),
			'edit' => User::user_access("edit lang"),
			'del'  => User::user_access("delete lang"),
			'add_other_lang'  => User::user_access("add other lang"),
		);
		$this->link = array(
			'add' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'add')),
            'edit' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'edit')),
            'del' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'delete')),
			'check' => WEB_ROOT.'/tools/recheckLang.php'
		);
		
		$this->link_js_me('admin_lang.js', __FILE__);
		$this->link_js('js/lang.js');
        $this->link_footer('<script type="text/javascript">shop.admin.lang.countries = '.json_encode(Language::$languages).'</script>');
	}

    function draw(){
        $title = Url::getParam('title', '');
        $lang = Url::getParam('lang', '');

		$cond = array();
        $cond[] = 'is_main = 1';
        if($lang != ''){
            $translateItems = DB::fetch_all("SELECT id, pid FROM ".T_LANG." WHERE is_main = 0 AND lang = '$lang' GROUP BY pid");
            $ids = array();
            foreach($translateItems as $data){
                $ids[] = $data['pid'];
            }
			if(!empty($ids)){
				$ids = implode(',',$ids);
				$cond[] = "id NOT IN ($ids)";
			}
        }
        if($title != ''){
            $cond[] = "title LIKE '%$title%'";
        }


        $items = array();
        $paging = '';
		$item_per_page = 50;
		
		$search_value = FunctionLib::addCondition($cond);
		$search_value = ($search_value != '') ? ' WHERE '.$search_value : '';
		
		$sql = 'SELECT  * FROM  '. $this->table . $search_value .' ORDER BY created DESC';
		
		$re = Pagging::pager_query($sql,$item_per_page);

		if($re){
			while ($row = mysql_fetch_assoc($re)){
                $row['trans'] = array();
				if($row['page_used'] != ''){
					$pages = unserialize($row['page_used']);
					$row['page_used'] = '';
					if(!empty($pages)){
						foreach($pages as $p => $v){
							$r = array(
								'name' => $p,
								'title' => isset(CGlobal::$arrPage[$p]) ? stripslashes(CGlobal::$arrPage[$p]['title']) : 'error',
								'link'  => Url::build($p)
							);
							$row['page_used'] .= '<div><a href="'.$r['link'].'" title="'.$r['name'].': '.$r['title'].'" target="_blank"'.($r['title'] == 'error' ? ' style="color:red"' : '').'>'.$r['title'].'</a></div>';
						}
					}
				}
				$row['created'] = FunctionLib::dateFormat($row['created'], 'd-m-Y');
				if($row['type'] != 1){
					$row['btn-edit'] = array('hide' => true); //neu tu he thong tu thu thap thi khong cho sua
				}
				$row['title'] = '<div id="word'.$row['id'].'">'.$row['title'].'</div>';
			    $items[$row['id']] = $row;
			}
			$paging = Pagging::getPager(5,false,'page_no', true);
		}
        //load thong tin ban dich
        if(!empty($items)){
            $re = DB::query("SELECT * FROM ".$this->table." WHERE is_main = 0 AND pid IN (".implode(',', array_keys($items)).")");
            while($row = @mysql_fetch_assoc($re)){
                $items[$row['pid']]['trans'][$row['lang']] = $row;
            }
			foreach($items as $idx => $item){
				foreach(Language::$listLangOptions as $k => $tit){
					if(isset($item['trans'][$k])){
						$items[$idx][$k] = '<a href="javascript:void(0)" onclick="shop.admin.lang.addTrans(\''.$k.'\', '.$item['id'].', '.$item['trans'][$k]['id'].')" id="ctrlLang_'.$k.'_'.$item['id'].'" style="color:darkblue">Sửa</a>
						<div id="trans_text_'.$k.'_'.$item['id'].'" style="display: none">'.$item['trans'][$k]['title'].'</div>';
					}else{
						$items[$idx][$k] = '<a href="javascript:void(0)" onclick="shop.admin.lang.addTrans(\''.$k.'\', '.$item['id'].')" id="ctrlLang_'.$k.'_'.$item['id'].'" style="color:red">Dịch</a>';
					}
				}
			}
        }

		Language::autoList($this, array(
			'items' => $items,
			'pagging' => array(
				'start_page' => (Pagging::$page-1)*$item_per_page,
				'total_item' => Pagging::$totalResult,
				'total_page' => Pagging::$totalPage,
				'pager'	=> $paging
			)
		));
    }

     function on_submit(){
        if(!empty($_POST['selected_ids'])){
			$ids = implode(',', $_POST['selected_ids']);
			DB::delete($this->table, "id IN ($ids) OR pid IN ($ids)");

			$this->setFormSucces('', "Xóa thành công! Từ gốc có mã ($ids) đã bị xóa");
		}else{
			$this->setFormError('', "Bạn chưa chọn từ gốc cần xóa");
		}
    }
}