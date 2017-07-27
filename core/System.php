<?php
class System{
	static function send_mail($fromEmail = '', $to = array(), $subject = '', $content = '', $images = array(), $toCC = array(),$toBCC = array(), $att = array()){
		$dev = ConfigSite::getConfigFromDB('smtp_dev', 0, false, 'site_configs');
		if($dev == 0){
			$debug = ConfigSite::getConfigFromDB('smtp_debug', false, false, 'site_configs');
			if($debug != 2){
				$debug = false;
			}
			$fromEmail = FunctionLib::is_valid_email($fromEmail) ? $fromEmail : ConfigSite::getConfigFromDB('smtp_from', false, false, 'site_configs');
			if(!FunctionLib::is_valid_email($fromEmail)){
				$fromEmail = 'noreply@'.DOMAIN_NAME_OK;
			}
	
			$mail = new PHPMailer();
			//$mail->Mailer		= 	SMTP_METHOD;
			$mail->SMTPAuth 	=	SMTP_AUTH;
			$mail->SMTPSecure	=	ConfigSite::getConfigFromDB('smtp_secure', '', false, 'site_configs');
			$mail->Priority 	=	1;
			$mail->SMTPDebug	=	$debug;			//debug php mailer
			$mail->CharSet 		=	'utf-8';
			$mail->Host     	=	ConfigSite::getConfigFromDB('smtp_host', '', false, 'site_configs');
			$mail->Port     	=	ConfigSite::getConfigFromDB('smtp_port', '', false, 'site_configs');
			$mail->Username 	=	ConfigSite::getConfigFromDB('smtp_user', '', false, 'site_configs');			// SMTP username
			$mail->Password		=	ConfigSite::getConfigFromDB('smtp_pass', '', false, 'site_configs');			// SMTP password
			$mail->From     	=	$fromEmail;	// Email duoc gui tu???
			$mail->FromName 	=	CGlobal::$site_name;	// Ten hom email duoc gui
			$mail->Subject  	=	$subject;			// Chu de email
			$mail->Body 		=	$content; //Noi dung html
			$mail->IsSMTP();
			$mail->IsHTML(true);		// Gui theo dang HTML
			$mail->AddAddress($to,"");	// Dia chi email va ten nhan
	
			//them CC 
			if(!empty($toCC)){
				foreach ($toCC as $cc=>$n){
					$mail->AddCC($cc,$n);
				}
			}
	
			//them BCC: bcc=email address | n=name
			if(!empty($toBCC)){
				foreach ($toBCC as $bcc=>$n){
					$mail->AddBCC($bcc,$n);
				}
			}
	
			//attach anh de hien thi luon
			if(!empty($images)){
				foreach($images as $img){
					if(!empty($img)){
						$ext = FileHandler::getExtension($img['src']);
						$moreExt = substr(strtolower($ext),0,strlen($ext));
						$img['mime'] = 'image/'.$moreExt;
						$mail->AddEmbeddedImage($img['src'], $img['id'], $img['title'].'.'.$moreExt, 'base64', $img['mime']);
					}
				}
				//cau hinh nhu sau <img src="cid:ubzsed" />
			}
	
			//attach them file
			if(!empty($att)){
				foreach ($att as $a){
					$mail->AddAttachment($a['path'],$a['name']);
				}
			}
	
			//gui email & tra ve
			return $mail->Send();
		}
		return true;
	}

	static function debug($array, $exit = false, $print_r = false, $trace = false){
		if(DEBUG){
			echo '<div align="left"><pre>';
			if($print_r){
				print_r($array);
			}else{
				var_dump($array);
			}
			echo '</pre></div>';
			if($trace){
				$backTrace = debug_backtrace();
				$backTrace = array_reverse($backTrace);
				$traceText = praseTrace($backTrace);
				echo '<h1>Trace</h1>';
				echo '<div class="mTop10">'.$traceText.'</div>';
			}
			if($exit) exit;
		}
	}
	static function bug_me($str = '', $echo = true, $exit = false, $reset = false, $html_mode = true){
		DebugSessionString::follow($str, $reset, $html_mode);
		if($echo){
			DebugSessionString::result($echo, $exit);
		}
	}
	static function halt(){
		exit();
	}
}

class DebugSessionString{
	static $key = 'debug-follow-me';
	static function follow($string = '', $reset = false, $html_mode = true){
		$key = DebugSessionString::$key;
		if(!isset($_SESSION[$key]) || $reset){
			$_SESSION[$key] = '';
		}
		if($html_mode){
			$string = '<div>'.$string.'</div>';
		}
		$_SESSION[$key] .= $string;
	}
	static function result($echo = true, $exit = false){
		$key = DebugSessionString::$key;
		if($echo && isset($_SESSION[$key])){
			echo $_SESSION[$key];
			if($exit){ exit; }
		}
		return $_SESSION[$key];
	}
}
