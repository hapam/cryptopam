<?php

class RunSqlForm extends Form {

	function __construct(){
		parent::__construct();
	}
	
	function draw() {
		global $display;
		
		#Get Du Lieu
		$table = Url::getParam('table','');
		$limit = Url::getParamInt("limit", 100);
		$conditions = isset($_GET['conditions']) ? $_GET['conditions'] : '';
		$field_orderby = Url::getParam("field_orderby", '');
		$orderby = Url::getParam("orderby", 'ASC');
		$field = Url::getParam("field", '*');
		
		#Chay Lenh
		if($table==''){
			$this->setFormSucces('', '<b>Cần chọn bảng nha olala :-p</b></br/>');
		}

		$listcolumn = array();
		$items = array();
		$paging = '';

		if($table!=''){
			$con = '';
			if($conditions!=''){
				$con =" WHERE ".$conditions;
			}
			$order = '';
			if($field_orderby!=''){
				$order =' ORDER BY '.$field_orderby." ".$orderby;
			}
			$sql = "SELECT ".$field." FROM ".$table." ".$con." ".$order;
			$checkSql = mysql_query($sql);
			if(!$checkSql){
				$this->setFormSucces('', '<b>Invalid query: ' . mysql_error().'</b></br/>');
			}else{
				$re = Pagging::pager_query($sql, $limit);
				if ($re) {
					while ($row = @mysql_fetch_assoc($re)) {
						if (empty($listcolumn)) {
							$listcolumn = array_keys($row);
						}
						$items[] = $row;
					}
					$paging = Pagging::getPager(3, false, 'page_no', true);
				}
			}
		}

		#Add TPL
		$display->add('listcolumn',$listcolumn);
		$display->add('items',$items);

		ViewCode::runSQLAuto($this, array(
			'items' => $items,
			'html_view_table' => $display->output('RunSql', true),
			'pagging' => array(
				'start_page' => (Pagging::$page-1)*$limit,
				'total_item' => Pagging::$totalResult,
				'total_page' => Pagging::$totalPage,
				'pager'	=> $paging
			)
		));
	}
}