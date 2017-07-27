<?php

class Authentication {
    static function getQRcode_user() {
        $project = CGlobal::$site_name.'-Login';

        //init GoogleAuth
        $ga = new PHPGangsta_GoogleAuthenticator();

        //Gen Secret key
        $secret = $ga->createSecret();

        //QR Code URL
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($project, $secret);

        //Get one token
        $oneCode = $ga->getCode($secret);

        //check token
        $checkResult = $ga->verifyCode($secret, $oneCode, 1);    // 1 = 1*30sec clock tolerance
        
        if ($checkResult) {
            return array('secret' => $secret, 'qrCodeUrl' => $qrCodeUrl);
        }
        return false;
    }
    
    static function checkValid2Step($uid = 0){
        if($uid > 0){
            $browserName = CookieLib::get_cookie(self::nameCookieBrowser($uid), '');
            if($browserName != ''){
                $time2ReOTP = ConfigSite::getConfigFromDB('log2step_time', 0, false, 'site_configs');
                //neu ghi nho mai mai
                if($time2ReOTP <= 0){
                    return true;
                }
                //kiem tra thoi gian
                $lastOTP = DB::fetch("SELECT * FROM ".T_USER_OTP." WHERE uid = $uid AND browser = '$browserName'");
                if($lastOTP){
                    if((time() - $lastOTP['lastOtp']) < $time2ReOTP*86400){
                        return true;
                    }else{
                        DB::delete(T_USER_OTP, "uid = $uid AND browser = '$browserName'");
                    }
                }
            }
        }
        return false;
    }
    
    static function saveOtpClient($uid = 0){
        $time2ReOTP = ConfigSite::getConfigFromDB('log2step_time', 0, false, 'site_configs');
        if($time2ReOTP <= 0){
            $time2ReOTP = 86400 * 365 * 3 + TIME_NOW; //luu trong 3 nam
        }else{
            $time2ReOTP = 86400 * $time2ReOTP + TIME_NOW;
        }

        //luu lai thoi gian nhap ma OTP dung
        $browserName = Authentication::nameBrowser($uid, TIME_NOW);
        DB::insert(T_USER_OTP, array('lastOtp' => TIME_NOW, 'uid' => $uid, 'browser' => $browserName));
        CookieLib::my_setcookie(Authentication::nameCookieBrowser($uid), $browserName, $time2ReOTP);

        //xoa cookie
        CookieLib::my_setcookie(md5("id_user_forstep2"), '', TIME_NOW - 3600);
    }
    
    static function nameCookieBrowser($uid = 0){
        return md5('BN-'.$uid);
    }
    
    static function nameBrowser($uid = 0, $time = 0){
        return md5('BN-'.$time.'-'.$uid);
    }
}
