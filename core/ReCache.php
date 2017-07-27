<?php
//@10 - 15 - 12 
//@Start redis
//@author tannv

class ReCache{
    static $expire = 3600,$subDir = '',$cacheKey = '',$store = array();

    static function set($cacheKey = '', $value = '', $expire = 0){
        if(CACHE_ON){
            if($cacheKey!=''){
                self::$cacheKey=$cacheKey;
                self::$expire=$expire;
                self::setCache($value);
            }
        }
        return true;
    }
    static function setCache ($value){
        $cacheKey=self::$cacheKey;
        if (REDIS_ON){
            CGlobal::$redis->set($cacheKey,$value,self::$expire);
        }

        //luu vao store
        self::$store[$cacheKey] = $value;
    }
    static function get($cacheKey = ''){
        $value = false;
        if(CACHE_ON && $cacheKey!=''){
            $hour = date('H',TIME_NOW);
            //kiem tra trong store xem co ton tai khong de lay ra
            if(isset(self::$store[$cacheKey])){
                $value = self::$store[$cacheKey];
            }
            if(empty($value)){
                if(REDIS_ON){
                    $value = CGlobal::$redis->get($cacheKey);
                }
                //luu vao store de lay ra lan sau
                self::$store[$cacheKey] = $value;
            }
        }
        return $value;
    }
    static function removePer($cacheKey = ''){
        if($cacheKey!=''){
            if (REDIS_ON){
                return CGlobal::$redis->delete($cacheKey);
//				return true;
            }
        }
        return false;
    }
    static function delete($cacheKey=''){
        if($cacheKey!='' && CACHE_ON){
            //xoa khoi store
            if(isset(self::$store[$cacheKey])){
                unset(self::$store[$cacheKey]);
            }
            return self::removePer($cacheKey);
        }
        return false;
    }
}
