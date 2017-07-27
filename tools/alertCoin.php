<?php
require_once '../core/Debug.php'; //System Debug...
require_once '../config/config.php';//System Config...
require_once '../core/Init.php';  //System Init...

//lay rate moi nhat
$timeArr = DB::fetch("SELECT created FROM ".T_COIN_RATE." GROUP BY created ORDER BY created DESC LIMIT 0,1");
echo '<p>TIME: '.FunctionLib::dateFormat($timeArr['created'], 'd/m H:i').'</p>';

$list = array();
$res = DB::query("SELECT * FROM ".T_COIN_RATE." WHERE created >= ".$timeArr['created']." GROUP BY pair_id ORDER BY created DESC");
while($r = @mysql_fetch_assoc($res)){
	$list[$r['pair_id']] = array(
		'price' => $r['last'],
		'time'  => $r['created']
	);
}
echo '<p>Get total: <b>'.count($list).'</b> items</p>';

//thong bao cho tung nguoi
$users = array();
$pairs = array();
$res = DB::query("SELECT * FROM ".T_COIN_USER." ORDER BY uid");
while($r = @mysql_fetch_assoc($res)){
	//echo $list[$r['pair_id']] .' - '. $r['price_alert'].'<br />';
	if(isset($list[$r['pair_id']]) && $list[$r['pair_id']]['price'] >= $r['price_alert']){
		if(!isset($users[$r['uid']])){
			$users[$r['uid']] = array();
		}
		$users[$r['uid']][$r['pair_id']] = $r;
		$pairs[$r['pair_id']] = $r['pair_id'];
	}
}

//lay ra cac pair can thong bao
if(!empty($pairs)){
	$pairs = DB::fetch_all("SELECT * FROM ".T_COIN_PAIR." WHERE id IN (".implode(',', $pairs).")");
}

echo '<p>Alert: <b>'.count($users).'</b> users</p>';

if(!empty($users)){
	//lay ra user
	$userArr = DB::fetch_all("SELECT id, email FROM ".T_USERS." WHERE id IN (".implode(',', array_keys($users)).")");

	foreach($users as $uid => $user){
		$content = '<p style="padding-bottom:10px">Dear,</p>';
		foreach($user as $pid => $v){
			$content.= '<p>'.$pairs[$pid]['pair'].' - '.$v['price'].' tăng lên <b>'.$list[$pid]['price'].'</b> ~ '.number_format(insert_calRate(array('buy' => $v['price'], 'now' => $list[$pid]['price'])),2).'% lúc '.FunctionLib::dateFormat($list[$pid]['time'], 'd/m H:i').'</p>';
		}
		$content.= '<p style="padding-top:20px">Best regards,<br />Pam</p>';

		if(sendEmail($userArr[$uid]['email'], "Cảnh báo coin tăng giá - ".FunctionLib::dateFormat(TIME_NOW, 'd/m H:i'), $content)){
			echo 'Success';
		}
	}
}

function sendEmail($to = '', $sub = '', $content = ''){
	$key = 'DKFHD^%$DJH123';
	$clientID = 1;

	$link = 'http://mail.ezcms.org/api.php';	
	$vars = array(
		'clientID' => $clientID,
		'publicKey'=> $key,
		'from' => 'noreply@goback.top',
		'fromName' => 'Coin Alert',
		'to' => $to,
		'toName' => '',
		'subject' => $sub,
		'content' => $content
	);
	
	$curl = new CURL();
	return $curl->post($link, $vars);
}