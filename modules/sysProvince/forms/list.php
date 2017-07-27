<?php
class ListProvinceForm extends Form{
	private $cmd = 'province', $table = T_PROVINCE;
	public $perm, $link;
    function __construct(){
		parent::__construct();
		$this->link_js_me('admin_province.js', __FILE__);
		$this->perm = array(
            'add' => User::user_access("add province"),
            'edit' => User::user_access("edit province"),
            'del' => User::user_access("delete province")
        );
		$this->link = array(
            'add' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'add')),
            'edit' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'edit')),
            'del' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'delete'))
        );
	}
	
	function draw(){
		$no_search = Url::getParamInt('no_search',0);
        $title      =   Url::getParam('title', '');
        $order_by   =   Url::getParam('order_by','status DESC, is_city DESC, position');
        $order_dir  =   Url::getParam('order_dir','ASC');

        $cond = 'status != -1';
        if($title != ''){
            $cond.=" AND title LIKE '%$title%'";
        }

        $item_per_page = 50;
        $items = array();
        $paging = '';
		
		if($no_search <= 0){
			$sql = 'SELECT  * FROM  '.$this->table .' WHERE '.$cond.' ORDER BY '.$order_by.' '.$order_dir;
			$re = Pagging::pager_query($sql,$item_per_page);
			if($re){
				while ($row = mysql_fetch_assoc($re)){
					$row['yahoo'] = ($row['yahoo'] != '') ? unserialize($row['yahoo']) : '' ;
					$row['skype'] = ($row['skype'] != '') ? unserialize($row['skype']) : '' ;
					$row['information'] = $this->makeInfomation($row);
					$row['contact'] = $this->makeContact($row);
					$row['status_icon'] = array(
						'icon' => 'check_circle',
						'color'=> ($row['status'] == 1) ? '' : 'grey'
					);
					
					$items[] = $row;
				}
				$paging = Pagging::getPager(3, false, 'page_no', true);
			}
		}
		Province::provinceAutoList($this, array(
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
			DB::delete(T_PROVINCE, "id IN ($ids)");
			$this->setFormSucces('', "Xóa thành công! Vùng miền có mã ($ids) đã bị xóa");
		}else{
			$this->setFormError('', "Bạn chưa chọn vùng miền cần xóa");
		}
    }
	
	function makeInfomation($data){
		$html = '';
		if($data['hotline']){
			$html .= '<b>Hotline:</b> '.$data['hotline'].'<br />';
		}
		if($data['fax']){
			$html .= '<b>Fax:</b> '.$data['fax'].'<br />';
		}
		if($data['email']){
			$html .= '<b>Email:</b> '.$data['email'].'<br />';
		}
		if($data['name_facebook']){
			$html .= '<b>Facebook:</b> '.$data['name_facebook'];
		}
		return $html;
	}
	
	function makeContact($data){
		$html = '';
		if($data['yahoo']){
			$html .= '<div class="m-t-10">Yahoo';
			foreach($data['yahoo'] as $id => $yahoo){
				$html .= '<div class="m-t-5"><b>'.$id.'</b><span class="mLeft10">('.$yahoo.')</span></div>';
			}
			$html .= '</div>';
		}
		if($data['skype']){
			$html .= '<div class="m-t-10">Skype';
			foreach($data['skype'] as $id => $skype){
				$html .= '<div class="m-t-5"><b>'.$id.'</b><span class="mLeft10">('.$skype.')</span></div>';
			}
			$html .= '</div>';
		}
		return $html;
	}
}