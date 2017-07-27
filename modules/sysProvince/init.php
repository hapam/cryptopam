<?php
	//init, dung de debug
	$sid = 'province_update_cached';
	$cacheKey = array(
		'province_active',
		'province',
		'messenger_support'
	);
	$time = array(86400*7, 86400*7, 86400*7);

	//check xem co can update ko
	$update = isset($_SESSION[$sid]) && $_SESSION[$sid];
	$_SESSION[$sid] = false;
	
	//lay cache
	CGlobal::$province_active = CacheLib::get($cacheKey[0], 86400*7);
	CGlobal::$province = CacheLib::get($cacheKey[1], 86400*7);
	CGlobal::$messenger_support = CacheLib::get($cacheKey[2], 86400*7);
	
	//kiem tra & tao lai cache neu can
	if($update || empty(CGlobal::$province) || empty(CGlobal::$province_active) || empty(CGlobal::$messenger_support)){
		CGlobal::$province = array();
		CGlobal::$messenger_support = array();
		CGlobal::$province_active = array();
		
		$res = DB::query("SELECT * FROM ".T_PROVINCE." ORDER BY is_city DESC, position ASC, safe_title ASC");
		while($row = @mysql_fetch_assoc($res)){
			//support
			if($row['status'] == '1'){
				$row['safe_name'] = $row['safe_title'];
				
				$yahoo = ($row['yahoo'] != '') ? @unserialize($row['yahoo']) : '' ;
				$skype = ($row['skype'] != '') ? @unserialize($row['skype']) : '' ;
				if(!empty($yahoo) || !empty($skype)){
					CGlobal::$messenger_support[$row['id']]['yahoo'] = $yahoo;
					CGlobal::$messenger_support[$row['id']]['skype'] = $skype;
				}
				CGlobal::$province_active[$row['id']] = $row;
			}else{
				unset($row['yahoo']);
				unset($row['skype']);
				unset($row['hotline']);
				unset($row['fax']);
				unset($row['email']);
				unset($row['address']);
				unset($row['name_facebook']);
				unset($row['safe_title']);
				unset($row['is_city']);
			}
			unset($row['position']);
			CGlobal::$province[$row['id']] = $row;
		}
		CacheLib::set($cacheKey[0], CGlobal::$province_active, $time[0]);
		CacheLib::set($cacheKey[1], CGlobal::$province, $time[1]);
		CacheLib::set($cacheKey[2], CGlobal::$messenger_support, $time[2]);
	}
