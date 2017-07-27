<?php
function insert_duration_time($arr){
	$time = intval($arr['time']);
	if($time > 0){
		return FunctionLib::duration_time($time);
	}
}

function insert_time_date($arr){
	$time = intval($arr['time']);
	if($time > 0){
		$date = getdate($arr['time']);
		$day_title = $date['wday'] == 0 ? 'Chủ nhật' : "Thứ ".($date['wday']+1);
		return "$day_title ngày ".$date['mday']." tháng ".$date['mon'];
	}
}

/* mb string */
function insert_mb_substr ($arr) {
	// kiem tra xem do dai cua chuoi co dai hon length ko
	$check = 0;
	$arr["str"] = trim($arr["str"]);
	$str = $arr["str"];
	if( mb_strlen($arr["str"], "utf-8") > $arr["length"] ) {
		$surfix = "...";
		$check = 1;
		$strTmp = mb_substr($arr["str"], 0, $arr["length"], "utf-8");
		$intPos = mb_strrpos($strTmp, " ", 0, "utf-8");
		$str = mb_substr($strTmp, 0, $intPos, 'utf-8').$surfix;
	}
	/*$arr = array("str" => $str, "check" => $check);
	return $arr;*/
	return $str;
}

function insert_has_role($arr){
	if(!empty($arr['role_ids'])){
		return isset($arr['role_ids'][$arr['rid']]);
	}
	return false;
}

function insert_user_access($arr){
	if(isset($arr['permit'])){
		return User::user_access($arr['permit']);
	}
	return false;
}

function insert_role_name($arr){
	if(isset($arr['role'])){
		return CGlobal::$permission_group[$arr['role']]['title'];
	}
	return 'None';
}

function insert_have_permit($arr){
	if(isset($arr['group']) && isset($arr['permit'])){
		$group_perm = CGlobal::$permission_group[$arr['group']]['permit'];
		$arr['permit'] = str_replace(' ','_',$arr['permit']);
		return stripos($group_perm.',', $arr['permit'].',') !== false;
	}
	return false;
}

function insert_counter ($arr) {
	return count($arr['v']);
}

function insert_vardump ($arr) {
	return var_dump($arr['v']);
}

function insert_countArray ($arr) {
	if(isset($arr['a'])){
		return count($arr['a']);
	}
	return 0;
}

function insert_priceFormat ($arr) {
	if(isset($arr['v'])){
		return FunctionLib::priceFormat($arr['v']);
	}
	return '0đ';
}

function insert_avatar ($arr) {
	if(!isset($arr['s'])){
		$arr['s'] = 45;
	}
	return FunctionLib::get_gravatar($arr['email'], $arr['s']);
}

function insert_jsonen ($arr) {
	$json = '';
	if(isset($arr['v'])){
		$json = json_encode($arr['v']);
	}
	return $json;
}

function insert_inarray ($arr) {
	if(isset($arr['a']) && isset($arr['i'])){
		if(!empty($arr['a'])){
			return in_array($arr['i'], $arr['a']);
		}
	}
	return false;
}

function insert_calRate ($arr) {
	if(isset($arr['buy']) && isset($arr['now'])){
		$rate = abs(($arr['buy'] - $arr['now'])*100/$arr['buy']);
		$loss = $arr['buy'] > $arr['now'];
		return $loss ? -$rate : $rate;
	}
	return 0;
}

function insert_calLoss ($arr) {
	if(isset($arr['buy']) && isset($arr['now']) && isset($arr['quan'])){
		$rate = insert_calRate($arr);

		return $arr['quan']*$rate/100;
	}
	return 0;
}