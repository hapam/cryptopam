<?php
/*
+--------------------------------------------------------------------------
|   MuaChung.vn
|   =============================================
|   by TanNv
|   =============================================
|   Web: http://muachung.vn
|   Started date : 12/02/2011
+---------------------------------------------------------------------------
*/
class redisPhp
{
    public $redisPhp;
    static $crashed;

    public function __construct() {
        if (!REDIS_ON) {
            return false;
        }
        if(!$this->redisPhp){
            $this->open ();
        }
    }

    public function open() {
        if(!$this->redisPhp && !redisPhp::$crashed){
            if (! CGlobal::$redis_server || ! count ( CGlobal::$redis_server )) {
                redisPhp::$crashed = 1;
                return false;
            }

            if (DEBUG) {
                $start_time = microtime ( true );
            }

            $this->redisPhp = new Redis();
            try {
                /**
                 * Connects to a Redis instance.
                 * <pre>
                 * $redis->connect('127.0.0.1', 6379);
                 * </pre>
                 */
                $this->redisPhp->connect(CGlobal::$redis_server[0]['host'], CGlobal::$redis_server[0]['port']);
                if( !$this->redisPhp ) {
                    throw new Exception("");
                }
            } catch( Exception $ex ) {
                return false;
            }

            if (DEBUG) {
                $end_time = microtime ( true );
                $load_time = round ( ($end_time - $start_time), 5 ) . "s";
                CGlobal::$conn_debug .= " <b>Connect to Redis server : ". print_r(CGlobal::$redis_server[0], true) ." </b> [in $load_time]<br>\n";
            }

            if (! $this->redisPhp) {
                predis::$crashed = 1;
                return false;
            }
        }
        return $this->redisPhp;
    }


    public function setOption( $name, $value ) {}

    /**
     * Get client option
     *
     * @param   string  $name parameter name
     * @return  Parameter value.
     * @example
     * // return Redis::SERIALIZER_NONE, Redis::SERIALIZER_PHP, or Redis::SERIALIZER_IGBINARY.
     * $redis->getOption(Redis::OPT_SERIALIZER);
     */
    public function getOption( $name ) {}

    /**
     * Get the value related to the specified key
     *
     * @param   string  $key
     * @return  string|bool: If key didn't exist, FALSE is returned. Otherwise, the value related to this key is returned.
     * @link    http://redis.io/commands/get
     */
    public function get( $key ) {
        $return = false;
        if(is_array($key) && count($key) > 0){
            $return = $this->redisPhp->getMult($key);
        }
        else{
            $return = $this->redisPhp->get($key);
        }
        if($return && DEBUG){

            $hash_key 	= md5($key );
            $backTrace = debug_backtrace();
            $backTrace = array_reverse($backTrace);
            $traceText = praseTrace($backTrace);
            $keyShow = base64_encode($key);
            CGlobal::$cacheDebug['redis']['get'][] =
            "<tr>
				<td bgcolor='#fff'><b style='color:#0039ba'>Get:</b> <b style='color:red;font-size:16px'>$key</b> <br /> <b style='color:#0039ba'>hash key:</b> $hash_key (<a href='javascript:void(0);' onclick=\"shop.deleteCache('$keyShow','redis')\">Delete</a>)</td>
			</tr>
			<tr>
				<td bgcolor='#fff'>
					<div id='redis-detail-$hash_key'><a href='javascript:void(0)'  onclick='document.getElementById(\"redis-$hash_key\").style.display = \"block\";document.getElementById(\"redis-detail-$hash_key\").style.display = \"none\";'>More detail...</a></div>
					<div style='display:none' id='redis-$hash_key'>
					<div>
						<b style='color:#0039ba'>Value : </b><a href='javascript:void(0)' onclick=\"shop.showCache('$keyShow','redis')\"'> Show value ...</a>
					</div>
					<div>
						$traceText
					</div>
					</div>
				</td>
			</tr>";
        }
        return @unserialize($return);
    }
    /**
     * Set the string value in argument as value of the key, with a time to live.
     * @param   string  $key
     * @param   string  $value
     * @param   int     $exp
     * @param   bool    $replace
     * @return  bool:   TRUE if the command is successful.
     */
    public function set( $key, $value, $exp = 0, $replace = true) {
        $return = false;
        $setValue = @serialize($value);
        if($replace){
            if($exp == 0){
                $return = $this->redisPhp->set($key,$setValue);
            }
            else {
                $return = $this->redisPhp->setex($key,$exp,$setValue);
            }
        }
        else{
            $return = $this->redisPhp->setnx($key,$setValue);
        }
        if($return && DEBUG && $key != 'slowQuery'){
            $hash_key 	= md5($key);
            $backTrace = debug_backtrace();
            $backTrace = array_reverse($backTrace);
            $traceText = praseTrace($backTrace);
            $keyShow = base64_encode($key);
            CGlobal::$cacheDebug['redis']['set'][] =
            "<tr>
				<td bgcolor='#fff'><b style='color:#0039ba'>Get:</b> <b style='color:red;font-size:16px'>$key</b> <br /> <b style='color:#0039ba'>hash key:</b> $hash_key (<a href='javascript:void(0);' onclick=\"shop.deleteCache('$keyShow','redis')\">Delete</a>)</td>
			</tr>
			<tr>
				<td bgcolor='#fff'>
					<div id='redis-set-detail-$hash_key'><a href='javascript:void(0)'  onclick='document.getElementById(\"redis-set-$hash_key\").style.display = \"block\";document.getElementById(\"redis-set-detail-$hash_key\").style.display = \"none\";'>More detail...</a></div>
					<div style='display:none' id='redis-set-$hash_key'>
					<div>
						<b style='color:#0039ba'>Value : </b>
							<a href='javascript:void(0)' onclick=\"shop.showCache('$keyShow','redis')\"> Show value ... </a>
							<div id='showValue$hash_key' style='display:none'></div>
					</div>
					<div>
						$traceText
					</div>
					</div>
				</td>
			</tr>";
        }
        return $return;
    }
    /**
     * Remove specified keys.
     *
     * @param   key|array   $key1 An array of keys, or an undefined number of parameters, each a key: key1 key2 key3 ... keyN
     * @return int Number of keys deleted.
     */
    public function delete( $key ) {
        return $this->redisPhp->del($key);
    }
    /**
     * Verify if the specified key exists.
     * @param   string $key
     * @return  bool: If the key exists, return TRUE, otherwise return FALSE.
     */
    public function exists( $key ) {
        return $this->redisPhp->exists($key);
    }
    /**
     * Increment the number stored at key by one.
     * @param   string $key
     * @param   int       $value  value that will be added to key
     * @return  int    the new value
     */
    public function incr( $key, $value = 0 ) {
        if((int) $value > 0){
            return $this->redisPhp->incr($key);
        }
        else{
            return $this->redisPhp->incrBy($key,$value);
        }
    }
    /**
     * Decrement the number stored at key by one.
     * @param   string $key
     * @param   int    $value  that will be substracted to key
     * @return  int    the new value
     */
    public function decr( $key, $value = 0 ) {
        if((int) $value > 0){
            return $this->redisPhp->decr($key);
        }
        else{
            return $this->redisPhp->decrBy($key,$value);
        }
    }
    /**
     * Adds the string value to the head (left) of the list. Creates the list if the key didn't exist.
     * If the key exists and is not a list, FALSE is returned.
     * @param   string $key
     * @param   string $value String, value to push in key
     * @param   string $postion push left or right
     * @param   bool   $checkExists
     * @return  int    The new length of the list in case of success, FALSE in case of Failure.
     */
    public function push($key, $value,$postion = 'left', $checkExists = false) {
        if(strtolower($postion) == 'left'){
            if(!$checkExists){
                return $this->redisPhp->lPush($key,$value);
            }
            else{
                return $this->redisPhp->lPushx($key,$value);
            }
        }
        else{
            if(!$checkExists){
                return $this->redisPhp->rPush($key,$value);
            }
            else{
                return $this->redisPhp->rPushx($key,$value);
            }
        }
    }
    /**
     * Returns and removes the last or first element of the list.
     * @param   string $key
     * @param   string $position pop first or last element
     * @return  string if command executed successfully BOOL FALSE in case of failure (empty list)
     */
    public function pop( $key, $position = 'left' ) {
        if(strtolower($postion) == 'left'){
            return $this->redisPhp->lPop($key);
        }
        else{
            return $this->redisPhp->rPop($key);
        }
    }
    /**
     * Returns the size of a list identified by Key. If the list didn't exist or is empty,
     * the command returns 0. If the data type identified by Key is not a list, the command return FALSE.
     *
     * @param   string  $key
     * @return  int     The size of the list identified by Key exists.
     * bool FALSE if the data type identified by Key is not list
     */
    public function countL( $key ) {
        return $this->redisPhp->lSize($key);
    }
    /**
     * Return the specified element of the list stored at the specified key.
     * Return FALSE in case of a bad index or a key that doesn't point to a list.
     * @param string    $key
     * @param int       $index
     * @return String the element at this index
     * Bool FALSE if the key identifies a non-string data type, or no value corresponds to this index in the list Key.
     */
    public function getL( $key, $index ) {
        return $this->redisPhp->lGet($key,$index);
    }


    /**
     * Set the list at index with the new value.
     *
     * @param string    $key
     * @param int       $index
     * @param string    $value
     * @return BOOL TRUE if the new value is setted. FALSE if the index is out of range, or data type identified by key
     * is not a list.
     */
    public function setL( $key, $index, $value ) {
        return $this->redisPhp->lSet($key,$index,$value);
    }


    /**
     * Returns the specified elements of the list stored at the specified key in
     * the range [start, end]. start and stop are interpretated as indices: 0 the first element,
     * 1 the second ... -1 the last element, -2 the penultimate ...
     * @param   string  $key
     * @param   int     $start
     * @param   int     $end
     * @return  array containing the values in specified range.
     */
    public function getRangeL( $key, $start, $end ) {
        return $this->redisPhp->lRange($key,$start,$end);
    }


    /**
     * Trims an existing list so that it will contain only a specified range of elements.
     *
     * @param string    $key
     * @param int       $start
     * @param int       $stop
     * @return array    Bool return FALSE if the key identify a non-list value.
     */
    public function trimL( $key, $start, $stop ) {
        return $this->redisPhp->lTrim($key, $start, $stop);
    }


    /**
     * Removes the first count occurences of the value element from the list.
     * If count is zero, all the matching elements are removed. If count is negative,
     * elements are removed from tail to head.
     *
     * @param   string  $key
     * @param   string  $value
     * @param   int     $count
     * @return  int     the number of elements to remove
     * bool FALSE if the value identified by key is not a list.
     */
    public function RemoveDupL( $key, $value, $count ) {
        return $this->redisPhp->lRem($key, $value, $count);
    }

    /**
     * Sets an expiration date (a timeout) on an item.
     *
     * @param   string  $key    The key that will disappear.
     * @param   int     $ttl    The key's remaining Time To Live, in seconds.
     * @return  bool:   TRUE in case of success, FALSE in case of failure.
     */
    public function setTimeout( $key, $ttl ) {
        return $this->redisPhp->setTimeout($key, $ttl);
    }

    /**
     * Returns the keys that match a certain pattern.
     *
     * @param   string  $pattern pattern, using '*' as a wildcard.
     * @return  array   of STRING: The keys that match a certain pattern.
     */
    public function getKeys( $pattern ) {
        return $this->redisPhp->keys($pattern);
    }

    /**
     * Returns the current database's size.
     *
     * @return int:     DB size, in number of keys.
     */
    public function dbSize( ) {
        return $this->redisPhp->dbSize();
    }

    /**
     * Returns the type of data pointed by a given key.
     *
     * @param   string  $key
     * @return Depending on the type of the data pointed by the key,
     * this method will return the following value:
     * - string: Redis::REDIS_STRING
     * - set:   Redis::REDIS_SET
     * - list:  Redis::REDIS_LIST
     * - zset:  Redis::REDIS_ZSET
     * - hash:  Redis::REDIS_HASH
     * - other: Redis::REDIS_NOT_FOUND
     */
    public function type( $key ) {
        return $this->redisPhp->type($key);
    }

    /**
     * Append specified string to the string stored in specified key.
     *
     * @param   string  $key
     * @param   string  $value
     * @return  int:    Size of the value after the append
     */
    public function append( $key, $value ) {
        return $this->redisPhp->append($key,$value);
    }



    /**
     * Return a substring of a larger string
     *
     * @deprecated
     * @param   string  $key
     * @param   int     $start
     * @param   int     $end
     */
    public function substr( $key, $start, $end ) {
        return $this->redisPhp->substr($key, $start, $end);
    }


    /**
     * Get the length of a string value.
     *
     * @param   string  $key
     * @return  int
     */
    public function strlen( $key ) {
        return $this->redisPhp->strlen($key);

    }

    /**
     * Removes all entries from the current database.
     *
     * @return  bool: Always TRUE.
     * @link    http://redis.io/commands/flushdb
     * @example $redis->flushDB();
     */
    public function flushDB() {
        $this->redisPhp->flushDB();
    }


    /**
     * Sort
     *
     * @param   string  $key
     * @param   array   $option array(key => value, ...) - optional, with the following keys and values:
     * - 'by' => 'some_pattern_*',
     * - 'limit' => array(0, 1),
     * - 'get' => 'some_other_pattern_*' or an array of patterns,
     * - 'sort' => 'asc' or 'desc',
     * - 'alpha' => TRUE,
     * - 'store' => 'external-key'
     * @return  array
     * An array of values, or a number corresponding to the number of elements stored if that was used.
     * @link    http://redis.io/commands/sort
     * @example
     * <pre>
     * $redis->delete('s');
     * $redis->sadd('s', 5);
     * $redis->sadd('s', 4);
     * $redis->sadd('s', 2);
     * $redis->sadd('s', 1);
     * $redis->sadd('s', 3);
     *
     * var_dump($redis->sort('s')); // 1,2,3,4,5
     * var_dump($redis->sort('s', array('sort' => 'desc'))); // 5,4,3,2,1
     * var_dump($redis->sort('s', array('sort' => 'desc', 'store' => 'out'))); // (int)5
     * </pre>
     */
    public function sort( $key, $option = null ) {
        return $this->redisPhp->sort($key,$option);
    }


    /**
     * Returns an associative array of strings and integers, with the following keys:
     *
     * - redis_version
     * - redis_git_sha1
     * - redis_git_dirty
     * - arch_bits
     * - multiplexing_api
     * - process_id
     * - uptime_in_seconds
     * - uptime_in_days
     * - lru_clock
     * - used_cpu_sys
     * - used_cpu_user
     * - used_cpu_sys_children
     * - used_cpu_user_children
     * - connected_clients
     * - connected_slaves
     * - client_longest_output_list
     * - client_biggest_input_buf
     * - blocked_clients
     * - used_memory
     * - used_memory_human
     * - used_memory_peak
     * - used_memory_peak_human
     * - mem_fragmentation_ratio
     * - mem_allocator
     * - loading
     * - aof_enabled
     * - changes_since_last_save
     * - bgsave_in_progress
     * - last_save_time
     * - total_connections_received
     * - total_commands_processed
     * - expired_keys
     * - evicted_keys
     * - keyspace_hits
     * - keyspace_misses
     * - hash_max_zipmap_entries
     * - hash_max_zipmap_value
     * - pubsub_channels
     * - pubsub_patterns
     * - latest_fork_usec
     * - vm_enabled
     * - role
     */
    public function info( ) {
        return $this->redisPhp->info();
    }

    /**
     * Sets multiple key-value pairs in one atomic command.
     * MSETNX only returns TRUE if all the keys were set (see SETNX).
     *
     * @param   array(key => value) $array Pairs: array(key => value, ...)
     * @return  bool    TRUE in case of success, FALSE in case of failure.
     * @link    http://redis.io/commands/mset
     * @example
     * <pre>
     * $redis->mset(array('key0' => 'value0', 'key1' => 'value1'));
     * var_dump($redis->get('key0'));
     * var_dump($redis->get('key1'));
     * // Output:
     * // string(6) "value0"
     * // string(6) "value1"
     * </pre>
     */
    public function mset( array $array ) {
        $this->redisPhp->mset($array);
    }


    /**
     * Adds a value to the hash stored at key. If this value is already in the hash, FALSE is returned.
     *
     * @param string $key
     * @param string $hashKey
     * @param string $value
     * @param bool 	 $replace
     * @return int
     * 1 if value didn't exist and was added successfully,
     * 0 if the value was already present and was replaced, FALSE if there was an error.
     * @link    http://redis.io/commands/hset
     * @example
     * <pre>
     * $redis->delete('h')
     * $redis->hSet('h', 'key1', 'hello');  // 1, 'key1' => 'hello' in the hash at "h"
     * $redis->hGet('h', 'key1');           // returns "hello"
     *
     * $redis->hSet('h', 'key1', 'plop');   // 0, value was replaced.
     * $redis->hGet('h', 'key1');           // returns "plop"
     * </pre>
     */
    public function hSet( $key, $hashKey, $value, $replace = true) {
        if($replace){
            return $this->redisPhp->hSet($key, $hashKey, $value);
        }
        else{
            return $this->redisPhp->hSetNx($key, $hashKey, $value);
        }
    }


    /**
     * Gets a value from the hash stored at key.
     * If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
     *
     * @param   string  $key
     * @param   string  $hashKey
     * @return  string  The value, if the command executed successfully BOOL FALSE in case of failure
     * @link    http://redis.io/commands/hget
     */
    public function hGet($key, $hashKey) {
        return $this->redisPhp->hGet($key, $hashKey);
    }

    /**
     * Returns the length of a hash, in number of items
     *
     * @param   string  $key
     * @return  int     the number of items in a hash, FALSE if the key doesn't exist or isn't a hash.
     * @link    http://redis.io/commands/hlen
     * @example
     * <pre>
     * $redis->delete('h')
     * $redis->hSet('h', 'key1', 'hello');
     * $redis->hSet('h', 'key2', 'plop');
     * $redis->hLen('h'); // returns 2
     * </pre>
     */
    public function hLen( $key ) {
        return $this->redisPhp->hLen($key);
    }

    /**
     * Removes a value from the hash stored at key.
     * If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
     *
     * @param   string  $key
     * @param   string  $hashKey
     * @link    http://redis.io/commands/hdel
     * @return  bool    TRUE in case of success, FALSE in case of failure
     */
    public function hDel( $key, $hashKey ) {
        return $this->redisPhp->hDel($key, $hashKey);
    }

    /**
     * Returns the keys in a hash, as an array of strings.
     *
     * @param   string  $key
     * @return  array   An array of elements, the keys of the hash. This works like PHP's array_keys().
     * @link    http://redis.io/commands/hkeys
     * @example
     * <pre>
     * $redis->delete('h');
     * $redis->hSet('h', 'a', 'x');
     * $redis->hSet('h', 'b', 'y');
     * $redis->hSet('h', 'c', 'z');
     * $redis->hSet('h', 'd', 't');
     * var_dump($redis->hKeys('h'));
     *
     * // Output:
     * // array(4) {
     * // [0]=>
     * // string(1) "a"
     * // [1]=>
     * // string(1) "b"
     * // [2]=>
     * // string(1) "c"
     * // [3]=>
     * // string(1) "d"
     * // }
     * // The order is random and corresponds to redis' own internal representation of the set structure.
     * </pre>
     */
    public function hKeys( $key ) {
        return $this->redisPhp->hKeys($key);
    }


    /**
     * Returns the whole hash, as an array of strings indexed by strings.
     *
     * @param   string  $key
     * @return  array   An array of elements, the contents of the hash.
     * @link    http://redis.io/commands/hgetall
     * @example
     * <pre>
     * $redis->delete('h');
     * $redis->hSet('h', 'a', 'x');
     * $redis->hSet('h', 'b', 'y');
     * $redis->hSet('h', 'c', 'z');
     * $redis->hSet('h', 'd', 't');
     * var_dump($redis->hGetAll('h'));
     *
     * // Output:
     * // array(4) {
     * //   ["a"]=>
     * //   string(1) "x"
     * //   ["b"]=>
     * //   string(1) "y"
     * //   ["c"]=>
     * //   string(1) "z"
     * //   ["d"]=>
     * //   string(1) "t"
     * // }
     * // The order is random and corresponds to redis' own internal representation of the set structure.
     * </pre>
     */
    public function hGetAll( $key ) {
        return $this->redisPhp->hGetAll($key);
    }

    /**
     * Increments the value of a member from a hash by a given amount.
     *
     * @param   string  $key
     * @param   string  $hashKey
     * @param   int     $value (integer) value that will be added to the member's value
     * @return  int     the new value
     * @link    http://redis.io/commands/hincrby
     * @example
     * <pre>
     * $redis->delete('h');
     * $redis->hIncrBy('h', 'x', 2); // returns 2: h[x] = 2 now.
     * $redis->hIncrBy('h', 'x', 1); // h[x] ← 2 + 1. Returns 3
     * </pre>
     */
    public function hIncrBy( $key, $hashKey, $value ) {
        return $this->hIncrBy($key, $hashKey, $value);
    }

    /**
     * Fills in a whole hash. Non-string values are converted to string, using the standard (string) cast.
     * NULL values are stored as empty strings
     *
     * @param   string  $key
     * @param   array   $hashKeys key → value array
     * @return  bool
     * @link    http://redis.io/commands/hmset
     * @example
     * <pre>
     * $redis->delete('user:1');
     * $redis->hMset('user:1', array('name' => 'Joe', 'salary' => 2000));
     * $redis->hIncrBy('user:1', 'salary', 100); // Joe earns 100 more now.
     * </pre>
     */
    public function hMset( $key, $hashKeys ) {
        return $this->redisPhp->hMset($key, $hashKeys);
    }

    /**
     * Retirieve the values associated to the specified fields in the hash.
     *
     * @param   string  $key
     * @param   array   $hashKeys
     * @return  array   Array An array of elements, the values of the specified fields in the hash,
     * with the hash keys as array keys.
     * @link    http://redis.io/commands/hmget
     * @example
     * <pre>
     * $redis->delete('h');
     * $redis->hSet('h', 'field1', 'value1');
     * $redis->hSet('h', 'field2', 'value2');
     * $redis->hmGet('h', array('field1', 'field2')); // returns array('field1' => 'value1', 'field2' => 'value2')
     * </pre>
     */
    public function hMGet( $key, $hashKeys ) {
        return $this->redisPhp->hMGet($key, $hashKeys);
    }

    /**
     * Verify if the specified member exists in a key.
     *
     * @param   string  $key
     * @param   string  $hashKey
     * @return  bool:   If the member exists in the hash table, return TRUE, otherwise return FALSE.
     * @link    http://redis.io/commands/hexists
     * @example
     * <pre>
     * $redis->hSet('h', 'a', 'x');
     * $redis->hExists('h', 'a');               //  TRUE
     * $redis->hExists('h', 'NonExistingKey');  // FALSE
     * </pre>
     */
    public function hExists( $key, $hashKey ) {
        return $this->redisPhp->hExists($key, $hashKey);
    }

}
