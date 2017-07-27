<?php
error_reporting ( 0 );
//debug enable
if (isset ( $_GET ["debug"] )) {
	define ( 'DEBUG', ( int ) ( boolean ) $_GET ["debug"] );
} elseif (isset ( $_COOKIE ["debug"] ) && intval ( $_COOKIE ["debug"] ) > 0) {
	define ( 'DEBUG', 1 );
} else {
	define ( 'DEBUG', 0 );
}

if (DEBUG) {
	error_reporting ( E_ALL );
	ini_set('display_errors', 1);
}

function getServerAddress(){
	if(isset($_SERVER['SERVER_ADDR'])){
		return $_SERVER['SERVER_ADDR'];
	}elseif(isset($_SERVER['LOCAL_ADDR'])){
		return $_SERVER['LOCAL_ADDR'];
	}
	return '127.0.0.1';
}

function getDebug(){
	//Set default debug
	if (isset ( $_GET ["debug"] )) {
		setcookie("debug", ( int ) ( boolean ) $_GET ["debug"], (time()+86400*365), "/");
	}
	global $start_rb;
	$mtime = microtime ();
	$mtime = explode ( " ", $mtime );
	$mtime = $mtime [1] + $mtime [0];
	$end_rb = $mtime;
	$page_load_time = round ( ($end_rb - $start_rb), 5 ) . "s";
	$time_now = date ( 'H:i:s - d-m-Y', TIME_NOW );
	$query_debug = CGlobal::$query_debug;
	$file_debug = CGlobal::$cacheDebug['file'];
	$mem_debug = CGlobal::$cacheDebug['mem']['get'];
	$mem_debug_set = CGlobal::$cacheDebug['mem']['set'];
	$queries_count = DB::num_queries ();
	$queries_time = CGlobal::$query_time;
	$conn_debug = CGlobal::$conn_debug;
	$sql_load_time = round ( $queries_time, 5 ) . "s";
	$includeFile = get_included_files();
	$server = getServerAddress();
	$buildPage = Url::buildAdminURL ( 'edit_page', array ('id' => Layout::$page ['id'] ) );
	$editPage = Url::buildAdminURL ( 'page', array ('cmd' => 'edit', 'id' => Layout::$page ['id']) );
	$delCache = Url::buildAdminURL ( 'page', array ('cmd' => 'refresh', 'id' => Layout::$page ['id'], 'href' => '?' . $_SERVER ['QUERY_STRING'] ) );
	
	$top = 
	$txt ="
	<center style='margin: 0 auto; padding: 10px 30px; background: #fff'>
		<div>
			Server: <b><font color=red>$server</font></b> |
			Số lượng query: <b>$queries_count</b> |
			Thời gian load trang: <b><font color=".(($page_load_time > 0.2)? "'red'" : "'green'").">$page_load_time </font></b> |
			Thời gian hiện tại: <b>$time_now</b>
		</div>
		<div style='margin-top:5px'>
			<a href='$buildPage'>Bố cục trang</a> |
			<a href='$editPage'>Sửa trang</a> |
			<a href='$delCache'>Xoá cache trang</a>
		</div>
		<div class='admin_debug'>
			<div>
				<h1>SQL Total Time: $sql_load_time for  $queries_count query</h1>
				<div style='color: #666'> $conn_debug </div>";
	if($query_debug){
		$txt.= 	"<div class='mTop10'>
					<table width='100%' border='0' cellpadding='6' cellspacing='1' bgcolor='#ababab' align='center'>
						<tr>
							<td colspan='8' style='background-color:#ababab;font-size:16px;color:#fff'><b>MySQL Query".($queries_count>0?" ($queries_count)":"")."</b></td>
						</tr>
						$query_debug
					</table>
				</div>";
	}
	if(!empty($file_debug['set'])){
		$txt.= 	"<div style='margin-top:10px'>
					<div align='left' id = 'Debug-Detail-File-set' style='font-size:16px'><a href='javascript:void(0)' onclick='document.getElementById(\"Debug-File-set\").style.display = \"block\";document.getElementById(\"Debug-Detail-File-set\").style.display = \"none\";'><b>File cache Set (".count($file_debug['set']).")</b></a></div>
					<div style='display:none' id ='Debug-File-set'>
						<table width='100%' border='0' cellpadding='6' cellspacing='1' bgcolor='navy'  align='center' style='font-size:14px'>
							<tr>
								<td colspan='8' style='background-color:navy;font-size:16px;color:#fff'><b>File cache Set (".count($file_debug['set']).")</b></td>
							</tr>
							".implode('', $file_debug['set'])."
						</table>
					</div> 
				</div>";
	}
	if(!empty($file_debug['get'])){
		$txt.= 	"<div style='margin-top:10px'>
					<div align='left' id = 'Debug-Detail-File' style='font-size:16px'><a href='javascript:void(0)' onclick='document.getElementById(\"Debug-File\").style.display = \"block\";document.getElementById(\"Debug-Detail-File\").style.display = \"none\";'><b>File cache (".count($file_debug['get']).")</b></a></div>
					<div style='display:none' id ='Debug-File'>
						<table width='100%' border='0' cellpadding='6' cellspacing='1' bgcolor='#ababab'  align='center' style='font-size:14px'>
							<tr>
								<td colspan='8' style='background-color:#ababab;font-size:16px;color:#fff'><b>File cache (".count($file_debug['get']).")</b></td>
							</tr>
							".implode('', $file_debug['get'])."
						</table>
					</div> 
				</div>";
	}
	
	if(!empty($mem_debug_set)){
		$txt.= 	"<div style='margin-top:10px'>
					<div align='left' id = 'Debug-Detail-Memcache-set' style='font-size:16px'><a href='javascript:void(0)'  onclick='document.getElementById(\"Debug-Memcache-set\").style.display = \"block\";document.getElementById(\"Debug-Detail-Memcache-set\").style.display = \"none\";'><b>Memcache Set (".count($mem_debug_set).")</b></a></div>
					<div style='display:none' id ='Debug-Memcache-set'>
						<table width='100%' border='0' cellpadding='6' cellspacing='1' bgcolor='navy'  align='center' style='font-size:14px'>
							<tr>
								<td colspan='8' style='background-color:navy;font-size:16px;color:#fff'><b>Memcache Set (".count($mem_debug_set).")</b></td>
							</tr>
							".implode('', $mem_debug_set)."
						</table>
					</div> 
				</div>";
	}
	if(!empty($mem_debug)){
		$txt.= 	"<div style='margin-top:10px'>
					<div align='left' id = 'Debug-Detail-Memcache' style='font-size:16px'><a href='javascript:void(0)'  onclick='document.getElementById(\"Debug-Memcache\").style.display = \"block\";document.getElementById(\"Debug-Detail-Memcache\").style.display = \"none\";'><b>MemCache (".count($mem_debug).")</b></a></div>
					<div style='display:none' id ='Debug-Memcache'>
						<table width='100%' border='0' cellpadding='6' cellspacing='1' bgcolor='#ababab'  align='center' style='font-size:14px'>
							<tr>
								<td colspan='8' style='background-color:#ababab;font-size:16px;color:#fff'><b>MemCache (".count($mem_debug).")</b></td>
							</tr>
							".implode('', $mem_debug)."
						</table>
					</div> 
				</div>";
	}
	if(!empty($includeFile)){
		$except = array('/_cache/','\_cache\\','/smarty/','\smarty\\','/core/','\core\\','index.php','class.php');
		$txt.= 	"<div style='margin-top:10px'>
					<div align='left' id = 'Include-Detail-Memcache' style='font-size:16px'><a href='javascript:void(0)'  onclick='document.getElementById(\"Include-Memcache\").style.display = \"block\";document.getElementById(\"Include-Detail-Memcache\").style.display = \"none\";'><b>PHP Include File (FILE_INCLUDE_NUMBER)</b></a></div>
					<div style='display:none' id ='Include-Memcache'>
						<table width='100%' border='0' cellpadding='6' cellspacing='1' bgcolor='#ababab'  align='center' style='font-size:14px'>
							<tr>
								<td style='background-color:#ababab;font-size:16px;color:#fff'><b>PHP Include File (FILE_INCLUDE_NUMBER)</b></td>
							</tr>";
		$k = 0;
		foreach ($includeFile as $i){
			$show = true;
			foreach ($except as $e){
				if (stripos($i, $e) > 0) {
					$show = false;
					break;
				}
			}
			if($show){
				$txt.="<tr>
					<td style='background-color:#fff;font-size:16px;color:#000'>".(++$k).".<span style='margin-left:5px'>$i</span></td>
				</tr>";
			}
		}
		$txt.= 	"		</table>
					</div> 
				</div>";
		$txt = str_replace('FILE_INCLUDE_NUMBER', $k, $txt);
	}
	
	if(!empty(AutoLoader::$list_file_loaded)) {
		$txt.= 	"<div style='margin:20px 0 0;padding:2px;border:#ffd850 1px solid;font-size:12px;'>
					<div style='background:#ffd850;font-size:16px;color:#f20000;margin:0 0 2px;padding:2px'><b>AUTOLOAD</b></div>
							<table width='100%' border='1' cellpadding='6' cellspacing='1' style='border-collapse: collapse; border-color: #cccccc' align='left'>
								<tr bgcolor='#CCCCCC'>
									<td style='padding-left: 40px;font-size:14px' bgcolor='#ffffb0'>
										<ul><li style='list-style-type:decimal;padding:2px'> ". join("</li><li style='list-style-type: decimal;padding:2px'>", AutoLoader::$list_file_loaded) ."</li></ul>
									</td>
								</tr>";
		$txt.= 	"			</table> 
					<div style='clear: both'></div>
				</div>";
	}
	
	if(!empty(CGlobal::$arrModuleDebug)) {
		$txt.= "<div style='margin:20px 0 0;font-size:12px;'>
					<div style='background:blue;font-size:16px;color:#fff;padding:10px'><b>DEBUG MODULE</b> TOTALGOHERE</div>
						<table width='100%' border='0' cellpadding='6' cellspacing='1' bgcolor='#ababab'>
							<tr bgcolor='#ccc'>
								<td align='center' width='30'><b>IDX</b></td>
								<td><b>NAME</b></td>
								<td width='100' align='center'><b>LOAD TIME</b></td>
								<td width='100' align='center'><b>DRAW TIME</b></td>
								<td width='100' align='center'><b>TOTAL TIME</b></td>
							</tr>";
		$total_time_all_module = 0;
		$k = 0;
		foreach(CGlobal::$arrModuleDebug as $m_name => $m){
			$total_time_module = $m['class'] + $m['draw'];
			$total_time_all_module += $total_time_module;
			$form_length = count($m['form']);
			$txt.=   "<tr bgcolor='#fff'>
				<td align='center'".($form_length>1 ? (" rowspan='".($form_length+1)."'"):"").">".(++$k)."</td>
				<td".($form_length>1 ? (" rowspan='".($form_length+1)."'"):"")."><b style='color:black'>$m_name</b></td>
				<td align='center'>".number_format($m['class'], 6)."</td>
				<td align='center'>".number_format($m['draw'],6)."</td>
				<td align='center'".($form_length>1 ? (" rowspan='".($form_length+1)."'"):"").(($total_time_module > 0.005) ? " style='color:white;background:#DD7EB1'":"").">".number_format($total_time_module,8)."</td>
			</tr>";
			if($form_length > 1){
				foreach($m['form'] as $form_name => $form_time){
					$txt.=   "<tr>
						<td><font color='green'>$form_name</font></td>
						<td".(($form_time > 0.005) ? " style='color:white;background:#DD7EB1'":"").">$form_time</td>
					</tr>";
				}
			}
		}
		$txt.= "</table> 
			<div style='clear: both'></div>
		</div>";
		$txt = str_replace('TOTALGOHERE', " - Total: $total_time_all_module", $txt);
	}
	
	if(!empty(Module::$init)) {
		$txt.= 	"<div style='margin:20px 0 0;padding:2px;border:#ffd850 1px solid;font-size:12px;'>
					<div style='background:#ffd850;font-size:16px;color:#f20000;margin:0 0 2px;padding:2px'><b>LOAD MODULE INIT</b></div>
							<table width='100%' border='1' cellpadding='6' cellspacing='1' style='border-collapse: collapse; border-color: #cccccc' align='left'>
								<tr bgcolor='#CCCCCC'>
									<td style='padding-left: 50px;font-size:14px' bgcolor='#ffffb0'>
										<ul><li style='list-style-type:decimal;padding:2px'> ". join("</li><li style='list-style-type: decimal;padding:2px'>", Module::$init) ."</li></ul>
									</td>
								</tr>";
		$txt.= 	"			</table> 
					<div style='clear: both'></div>
				</div>";
	}

	$txt.= "
			</div>
		</div>
	</center>";
	
	return $txt;
}
function praseTrace($backTrace){
	$traceText = '';
	if(!empty($backTrace)){
		$traceText = 
		"<table width='100%' border='0' cellpadding='6' cellspacing='0' align='center'>";
		$i = 0;
		$except = array('index.php','Module.php','Layout.php','Form.php','_cache','LayoutGen.php','CacheLib.php');
		foreach ($backTrace as $b){
			$show = true;
			foreach ($except as $e){
				if (isset($b['file']) && stripos($b['file'], $e) > 0) {
					$show = false;
					break;
				}
			}
			if ($show) {
				$traceText .= '<tr>';
				$traceText .= '<td '.(($i !=0) ? 'style ="border-top:1px solid #ccc"' : '' ).'>';
				$traceText .= '<div style="overflow:hidden;height:17px">';
				$file = $b['file'];
				$lenFile = strlen($file);
				if($lenFile > 35){
					$file = '...'.substr($file, ($lenFile - 35), $lenFile);
				}
				$traceText .= '<div style="float:left;width:350px"><b>File : </b><span title=\''.$b['file'].'\'>'.$file.'</span></div>';
				$traceText .= '<div style="float:left;width:80px"><b>Line:</b> '.$b['line'].'</div>';
				//if(isset($b['class']) || isset($b['function'])){
				//	$traceText .= '<div style="float:left;margin:0 20px;width:500px"> ';
				//	$funcText = '';
				//	if(isset($b['class'])){
				//		$funcText = $b['class'].((isset($b['type'])) ? $b['type'] : '');
				//	}
				//	if(isset($b['function'])){
				//		$funcText .= $b['function'];
				//	}
				//	$funcText .= '(';
				//	if(isset($b['args'])){
				//		$valText = '';
				//		foreach ($b['args'] as $v){
				//			if(is_integer($v)){
				//				$valText .= $v.',';
				//			}
				//			elseif (is_bool($v)){
				//				$valText .= ($v ? 'true' : 'false').',';
				//			}
				//			elseif (is_array($v)){
				//				$valText .= '"'.json_encode($v).'",';
				//			}elseif (is_string($v)){
				//				$valText .= '"'.$v.'",';
				//			}
				//		}
				//		$funcText .= (strlen($valText) > 1) ? substr($valText,0,-1) : $valText;
				//	}
				//	$funcText .= ');';
				//	$shortText = $funcText;
				//	if(strlen($shortText) > 60){
				//		$shortText = substr($funcText, 0, 60).'...';
				//	}
				//	$traceText .= '<span title=\''.$funcText.'\'>'.$shortText.'</span></div>';
				//}
				$traceText .= '<div style="clear:both;height:0;overflow:hidden"></div></div></td></tr>';
				$i++;
			}
		}
		$traceText .= "</table>";
	}
	return $traceText;
}
