<?php
class ListmyCoinForm extends Form {
    private $cmd = 'crypto', $table = T_COIN;
	public $perm, $link;
    function __construct() {
		parent::__construct();
	    $this->perm = array(
			'add' => User::user_access("admin myCoin"),
			'edit' => User::user_access("edit myCoin"),
			'del' => User::user_access("delete myCoin")
        );
		$this->link = array(
			'add' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'add')),
            'edit' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'edit')),
			'del' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'delete'))
        );
	}

    function draw() {
        global $display;
		$name = Url::getParam('name', '');
		$title = Url::getParam('title', '');
        $created_time_from = Url::getParam('created_time', '');
        $created_time_to = Url::getParam('created_time_to', '');
        $order_by = Url::getParam('order_by', 'id');
        $order_dir = Url::getParam('order_dir', 'DESC');

        $cond = array();
		if($name != ''){
			$cond[] = "name LIKE '%".$name."%'";
		}
		if($title != ''){
			$cond[] = "title LIKE '%".$title."%'";
		}
		// search time
        if ($created_time_from) {
            $date_arr = explode('-', $created_time_from);
            if (isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])) {
                $created_time_from = mktime(0, 0, 0, (int) $date_arr[1], (int) $date_arr[0], (int) $date_arr[2]);
                $cond[] = "created >= $created_time_from";
			}
		}
        if ($created_time_to) {
            $date_arr = explode('-', $created_time_to);
            if (isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])) {
                $created_time_to = mktime(23, 59, 59, (int) $date_arr[1], (int) $date_arr[0], (int) $date_arr[2]);
                $cond[] = "created <= $created_time_to";
            }
        }

        $data = array();
        $paging = '';
        $item_per_page = 20;

        $search_value = FunctionLib::addCondition($cond);
        $search_value = ($search_value != '') ? ' WHERE ' . $search_value : '';

        $sql = 'SELECT * FROM  ' . $this->table . $search_value . ' ORDER BY ' . $order_by . ' ' . $order_dir;
        $re = Pagging::pager_query($sql, $item_per_page);
        if ($re) {
            while ($r = mysql_fetch_assoc($re)) {
				$r['created'] = $r['created'] > 0 ? FunctionLib::dateFormat($r['created'], 'd/m/Y H:i:s') : '---';
                $data[$r['id']] = $r;
            }
            $paging = Pagging::getPager(3, false, 'page_no', true);
        }

		Crypto::autoList($this, array(
			'items' => $data,
			'pagging' => array(
				'start_page' => (Pagging::$page-1)*$item_per_page,
				'total_item' => Pagging::$totalResult,
				'total_page' => Pagging::$totalPage,
				'pager'	=> $paging
			)
		));
    }

    function on_submit() {
        if (!empty($_POST['selected_ids'])) {
            $ids = implode(',', $_POST['selected_ids']);
            DB::update($this->table, array("status" => -1), " id IN ($ids)");

            $this->setFormSucces('', "Xóa thành công! Bản ghi có mã ($ids) đã bị xóa");
        } else {
            $this->setFormError('', "Bạn chưa chọn bản ghi cần xóa");
        }
    }
}