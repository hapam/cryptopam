<?php
class ListBackupForm extends Form {
    private $cmd = 'backup', $table = T_BACKUP;
	public $perm, $link;
    function __construct() {
		parent::__construct();
	    $this->perm = array(
			'add' => User::user_access("restore backup"),
			'edit' => User::user_access("restore backup"),
			'del' => User::user_access("delete backup")
        );
		
		$this->link_js_me('backup.js', __FILE__);
	}

    function draw() {
		//search time
		$time_from = Url::getParam('created_time','');
		$time_to = Url::getParam('created_time_to','');
		
		$order_by = Url::getParam('order_by', 'created');
        $order_dir = Url::getParam('order_dir', 'DESC');

        $cond = array();
		// search time
		if($time_from){
			$date_arr = explode('-',$time_from);
			if(isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])){
				$created_time_from = mktime(0,0,0,(int)$date_arr[1],(int)$date_arr[0],(int)$date_arr[2]);
				$cond[] = "created >= $created_time_from";
			}
		}
		if($time_to){
			$date_arr = explode('-',$time_to);
			if(isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])){
				$created_time_to = mktime(23,59,59,(int)$date_arr[1],(int)$date_arr[0],(int)$date_arr[2]);
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
				$r['download'] = Url::downloadLink($r['name'], BACKUP_FOLDER, $r['created']);
				$r['created'] = FunctionLib::dateFormat($r['created'], 'd/m/Y H:i:s');
				$r['delete'] = "javascript:shop.backup.del(".$r['id'].", '".$r['created']."')";
				$r['restore'] = "javascript:shop.backup.restore(".$r['id'].", '".$r['created']."')";
                $data[$r['id']] = $r;
            }
            $paging = Pagging::getPager(3, false, 'page_no', true);
        }
		
		Backup::autoList($this, array(
			'items' => $data,
			'pagging' => array(
				'start_page' => (Pagging::$page-1)*$item_per_page,
				'total_item' => Pagging::$totalResult,
				'total_page' => Pagging::$totalPage,
				'pager'	=> $paging
			)
		));
    }
}