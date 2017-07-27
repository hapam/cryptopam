<?php

class memcacheLib{
	static $identifier, $crashed = 0, $encoding_mode, $debug = 1;

	function memcacheLib(){}

	static function connect(){
		if(!CGlobal::$memcache_connect_id && !memcacheLib::$crashed){
			if( !function_exists('memcache_connect') ){
				memcacheLib::$crashed = 1;
				return FALSE;
			}

			memcacheLib::$identifier = MEMCACHE_ID;

			if(!CGlobal::$memcache_server || !count(CGlobal::$memcache_server) ){
				memcacheLib::$crashed = 1;
				return FALSE;
			}

		    for ($i = 0, $n = count(CGlobal::$memcache_server); $i < $n; $i++){

		        $server = CGlobal::$memcache_server[$i];

		        if( $i < 1 ) {
		       		 CGlobal::$memcache_connect_id = @memcache_connect($server['host'], $server['port']);
		        }
		        else {
					memcache_add_server( CGlobal::$memcache_connect_id, $server['host'], $server['port'] );
		        }
		    }

			//memcache_debug( memcacheLib::$debug );
		}
		return CGlobal::$memcache_connect_id;
	}


	static function disconnect(){
		if( CGlobal::$memcache_connect_id ){
			memcache_close( CGlobal::$memcache_connect_id );
		}

		return TRUE;
	}

	static function stats(){
		if(self::connect()){
			if( CGlobal::$memcache_connect_id ){
				return	memcache_get_stats( CGlobal::$memcache_connect_id );
			}
		}

		return TRUE;
	}

	static function do_put( $key, $value, $ttl=0 ){
		if(self::connect()){
			$return = memcache_set( CGlobal::$memcache_connect_id, md5( memcacheLib::$identifier . $key ),
								$value,
								MEMCACHE_COMPRESSED,
								intval($ttl) );
			if($return && DEBUG && $key != 'slowQuery'){
	    		$hash_key 	= md5($key);
				$backTrace = debug_backtrace();
				$backTrace = array_reverse($backTrace);
				$traceText = praseTrace($backTrace);
				CGlobal::$cacheDebug['mem']['set'][] = 
				"<tr>
					<td bgcolor='#fff'><b style='color:#0039ba'>Set:</b> <b style='color:red;font-size:16px'>$key</b> <br /></td>
				</tr>
				<tr>
					<td bgcolor='#fff'>
						<div id='memcache-set-detail-$hash_key'><a href='javascript:void(0)'  onclick='document.getElementById(\"memcache-set-$hash_key\").style.display = \"block\";document.getElementById(\"memcache-set-detail-$hash_key\").style.display = \"none\";'>More detail...</a></div>
						<div style='display:none' id='memcache-set-$hash_key'>
						<div>
							<b style='color:#0039ba'>Value : </b>
								<a href='javascript:void(0)' onclick=\"shop.showCache('".addslashes($key)."', $ttl)\"> Show value </a>
								<div id='showValue$hash_key' style='display:none'></div>
						</div>
						<div>
							$traceText
						</div>
						</div>
					</td>
				</tr>";
			}
			
		}
	}

	static function do_get( $key ){
		if(self::connect()){
			$hash_key 	= md5( memcacheLib::$identifier . $key );
			$return_val = memcache_get( CGlobal::$memcache_connect_id, $hash_key);
	  		if($return_val){
				if(DEBUG){
					$backTrace = debug_backtrace();
					$backTrace = array_reverse($backTrace);
					$traceText = praseTrace($backTrace);
					CGlobal::$cacheDebug['mem']['get'][] = 
					"<tr id='cache-con-$hash_key'>
						<td bgcolor='#fff'>
							<div><b style='color:#0039ba'>Get:</b> <b style='color:red;font-size:16px'>$key</b> <br /> <b style='color:#0039ba'>hash key:</b> $hash_key (<a href='javascript:void(0);' onclick=\"shop.deleteCache('$key')\">Delete</a>)</div>
							<div id='memcache-detail-$hash_key'><a href='javascript:void(0)'  onclick='document.getElementById(\"memcache-$hash_key\").style.display = \"block\";document.getElementById(\"memcache-detail-$hash_key\").style.display = \"none\";'>More detail...</a></div>
							<div style='display:none' id='memcache-$hash_key'>
								<div>
									<b style='color:#0039ba'>Value : </b>
										<a href='javascript:void(0)' onclick=\"shop.showCache('".addslashes($key)."')\"> Show value ... </a>
										<div id='showValue$hash_key' style='display:none'></div>
								</div>
								<div>
									$traceText
								</div>
							</div>
						</td>
					</tr>";
				}
				return $return_val;
	  		}
  		}
  		return false;
	}

	static function do_remove( $key ){
		if(self::connect()){
			memcache_delete( CGlobal::$memcache_connect_id, md5( memcacheLib::$identifier . $key ) );
		}
	}

	static function clear(){
		if(self::connect()){
			memcache_flush (CGlobal::$memcache_connect_id );
		}
		return true;
    }
}