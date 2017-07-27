<?php
class Pagging{
	static $totalPage = 1, $recPerPage = 10, $page = 1, $totalResult = 0;

	static function pager_query(&$sql = '',$itemperpage=10,$page_name = 'page_no'){
		if(self::getTotalPage($sql, $itemperpage)){
			$page = Url::getParamInt($page_name, 1);
			if($page <= 0){
				$page = 1;
			}
			if($page > self::$totalPage){
				$page = self::$totalPage;
			}
			self::$page = $page;
			self::$recPerPage = $itemperpage;

			$stripos_limit = stripos($sql,'limit');
			if($stripos_limit !== false){
				$sql = substr($sql, 0, $stripos_limit);
			}
			$sql .= ' LIMIT '.($page - 1)*$itemperpage.', '.$itemperpage;
			return DB::query($sql);
		}
		return false;
	}

	static function getTotalPage($sql = '', $itemperpage = 10){
		if($itemperpage > 0){
			$count = "SELECT count(*) as total ".substr($sql, stripos($sql,'from'), strlen($sql));
			$count = DB::query($count);
			if($count = @mysql_fetch_assoc($count)){
				self::$totalResult = $count['total'];
				return self::$totalPage = ceil(self::$totalResult/$itemperpage);
			}
		}
		return false;
	}

	static function getPager($numPageShow = 10, $showTotalPage = false, $page_name = 'page_no', $adminStyle = false){
		if(self::$totalPage == 1) return '';

		if($adminStyle){
			return self::getPagerAdmin($numPageShow, $showTotalPage, $page_name);
		}

		$next = ''; $last = ''; $prev = ''; $first= ''; $left_dot  = ''; $right_dot = '';
		$from_page = self::$page - $numPageShow;
		$to_page = self::$page + $numPageShow;
		
		//get prev & first link
		if(self::$page > 1){
			$prev = self::parseLink(self::$page-1, 'page-item prev', "&lt; Trước", $page_name);
			$first= self::parseLink(1, 'page-item first', "&laquo; Đầu", $page_name);
		}
		//get next & last link
		if(self::$page < self::$totalPage){
			$next = self::parseLink(self::$page+1, 'page-item next', "Sau &gt;", $page_name);
			$last = self::parseLink(self::$totalPage, 'page-item last', "Cuối &raquo;", $page_name);
		}
		//get dots & from_page & to_page
		if($from_page > 0)	{
			$left_dot = ($from_page > 1) ? '<span class="dot">...</span>' : '';
		}else{
			$from_page = 1;
		}

		if($to_page < self::$totalPage)	{
			$right_dot = '<span class="dot">...</span>';
		}else{
			$to_page = self::$totalPage;
		}

		$pagerHtml = '';
		for($i=$from_page;$i<=$to_page;$i++){
			$cl = (self::$page == $i) ? 'page-item active' : 'page-item';
			$pagerHtml .= self::parseLink($i, $cl, $i, $page_name, $adminStyle);
		}
		return '<div class="pager">'.$first.$prev.$left_dot.$pagerHtml.$right_dot.$next.$last.'</div><div class="c"></div>';
	}
	
	static function getPagerAdmin($numPageShow = 10, $showTotalPage = false, $page_name = 'page_no'){
		$next = '';$last = '';$prev = '';$first= '';$left_dot  = '';$right_dot = '';
		$from_page = self::$page - $numPageShow;
		$to_page = self::$page + $numPageShow;
		
		//get prev & first link
		$cl = (self::$page <= 1)?'disabled':'';
		$prev = self::parseLink(self::$page-1, $cl, '<i class="material-icons">chevron_left</i>', $page_name, true);
		//$first= self::parseLink(1, $cl, '<i class="material-icons">first_page</i>', $page_name, true);
		
		//get next & last link
		$cl = (self::$page >= self::$totalPage)?'disabled':'';
		$next = self::parseLink(self::$page+1, $cl, '<i class="material-icons">chevron_right</i>', $page_name, true);
		//$last = self::parseLink(self::$totalPage, $cl, '<i class="material-icons">last_page</i>', $page_name, true);
		
		//get dots & from_page & to_page
		if($from_page > 0)	{
			//$left_dot = ($from_page > 1) ? '<li class="disabled"><a href="javascript:void(0)"><i class="material-icons">more_horiz</i></a></li>' : '';
		}else{
			$from_page = 1;
		}

		if($to_page < self::$totalPage)	{
			//$right_dot = '<li class="disabled"><a href="javascript:void(0)"><i class="material-icons">more_horiz</i></a></li>';
		}else{
			$to_page = self::$totalPage;
		}

		$pagerHtml = '';
		for($i=$from_page;$i<=$to_page;$i++){
			$cl = (self::$page == $i) ? 'active' : '';
			$pagerHtml .= self::parseLink($i, $cl, $i, $page_name, true);
		}
		return '<nav><ul class="pagination">'.$first.$prev.$left_dot.$pagerHtml.$right_dot.$next.$last.'</ul></nav>';
	}

	static function parseLink($page = 1, $class="", $title="", $page_name = 'page_no', $adminStyle = false, $id=""){
		$pageURL = '';
		if(!REWRITE_ON){
			$count = 0;
			$pageURL = preg_replace('@page_no=[0-9]@i',"page_no=$page", $_SERVER['QUERY_STRING'], -1, $count);
			if($count == 0){
				$pageURL = str_replace('?','&', $pageURL)."&page_no=$page";
			}
			$pageURL = '?'.$pageURL;
		}else{
			if(Url::isAdminUrl()){
				$params = CGlobal::$urlVars;
				$params['page_no'] = $page;
				if(!isset($params['cmd']) && isset(CGlobal::$urlArgs[1])){
					$params['cmd'] = CGlobal::$urlArgs[1];
				}
				$pageURL = Url::buildAdminURL(CGlobal::$current_page, $params);
			}else{
				$params = CGlobal::$urlArgs;
				unset($params[0]);
                $values = CGlobal::$urlVars;
                $values['page_no'] = $page;
                $pageURL = Url::build(CGlobal::$current_page, $params, '?'.http_build_query($values));
			}
		}
		if($adminStyle){
			if($class != ''){
				return '<li class="'.$class.'"><a href="javascript:void(0)">'.$title.'</a></li>';
			}
			return '<li><a href="'.$pageURL.'" class="waves-effect">'.$title.'</a></li>';
		}
		return '<a href="'.$pageURL.'" class="'.$class.'" '.($id!=''?('id="'.$id.'"'):'').' title="Xem trang '.$title.'">'.$title.'</a>';
	}
}
