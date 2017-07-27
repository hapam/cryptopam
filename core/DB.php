<?php
class DB {
	static $db_connect_id = false; // connection id of this database
	static $db_result = false; // current result of an query
	static $db_num_queries = 0;
	// Debug
	static $num_queries = 0; // number of queries was done
	static $query_debug = "";
	static $query_time;
	
	static $replicate_query = true; // mac dinh cho tat ca query, neu co quey khong dung replicate : false, xu ly xong phai tra ve true.
	static $master_connect = false; // current result of an query
	
	
	static function db_connect($sqlserver, $sqluser, $sqlpassword, $dbname) {
		
		if (DEBUG) {
			$rtime = microtime ();
			$rtime = explode ( " ", $rtime );
			$rtime = $rtime [1] + $rtime [0];
			$start_time = $rtime;
		}
		
		$db_connect_id = @mysql_connect ( $sqlserver, $sqluser, $sqlpassword, true );
		if (isset ( $db_connect_id ) and $db_connect_id) {
			if (! $dbselect = @mysql_select_db ( $dbname )) {
				@mysql_close ( $db_connect_id );
				$db_connect_id = $dbselect;
			}
			//set default charset DB 
			//added by Nova / 08.11.08
			if (DB_CHARSET == 'UTF8') {
				mysql_query ( 'SET NAMES UTF8', $db_connect_id );
			}
			
			if (DEBUG) {
				$rtime = microtime ();
				$rtime = explode ( " ", $rtime );
				$rtime = $rtime [1] + $rtime [0];
				$end_time = $rtime;
				$doing_time = round ( ($end_time - $start_time), 5 ) . "s";
				
				CGlobal::$conn_debug .= " <b>Connect to mysql server : $sqlserver - $db_connect_id </b>[In $doing_time]<br>\n";
			}
		}
		
		if (! $db_connect_id) {
			die ( 'Error: Could not connect to the database!' );
			return false;
		}
		
		return $db_connect_id;
	}
	static function re_connect(){
		DB::close();
		self::$master_connect = self::db_connect ( DB_MASTER_SERVER, DB_MASTER_USER, DB_MASTER_PASSWORD, DB_MASTER_NAME );
	}
	static function query($query) {
		self::$db_result = false;
		
		if (! empty ( $query )) {
			//lay thoi gian bat dau query
			if (DEBUG) {
				$rtime = microtime ();
				$rtime = explode ( " ", $rtime );
				$rtime = $rtime [1] + $rtime [0];
				$start_time = $rtime;
			}
			
			if (! self::$master_connect) {
				self::$master_connect = self::db_connect ( DB_MASTER_SERVER, DB_MASTER_USER, DB_MASTER_PASSWORD, DB_MASTER_NAME );
			}
			$connection_switch = self::$master_connect;
			
			self::$db_connect_id = $connection_switch;
			
			if (! (self::$db_result = @mysql_query ( $query, self::$db_connect_id ))) {
				//gui email khi co loi truy van
				if(!preg_match('#localhost#', WEB_ROOT)) {
					$email_bug = EMAIL_NOTIFY_DB;
					$server = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : (isset($_SERVER['LOCAL_ADDR'])?$_SERVER['LOCAL_ADDR']:'127.0.0.1');
					$content = 'Time: '.date('d-M-Y H:i:s').'<br />';
					$content.= 'Server: '. $server .'<br />';
					$content.= '<b>mysql_error: '.mysql_error(self::$db_connect_id).'  in <br />['.$query .']</b><br />';
					$content.= '<pre>'.print_r(debug_backtrace(), true).'</pre><br />-- end error --';
					System::send_mail('System',	$email_bug, CGlobal::$site_name.' SQL Error - '.WEB_ROOT.' - '.date('d-M-Y H:i'),	$content);
				}
				if (DEBUG) {
					echo '<p><font face="Courier New,Courier" size=3><b>' . mysql_error ( self::$db_connect_id ) . '  in ' . $query . '</b></font><br><b>Run at:</b><br />';
					System::debug(debug_backtrace());
					exit ();
				} else {
					echo '<p><font face="Courier New,Courier" size=3><b>Có lỗi truy vấn cơ sở dữ liệu</b></font><br>';
					exit ();
				}
			}
			self::$db_num_queries ++;
			//thoi gian sau khi query
			$rtime = microtime ();
			$rtime = explode ( " ", $rtime );
			$rtime = $rtime [1] + $rtime [0];
			$end_time = $rtime;
			$doing_time = round ( ($end_time - $start_time), 5 ) . "s";
			if (DEBUG) {
				$effect_rows = mysql_affected_rows ( self::$db_connect_id );
				$backTrace = debug_backtrace();
				$backTrace = array_reverse($backTrace);
				$traceText = praseTrace($backTrace);
				$hash_key = md5($query.$rtime);
				if (preg_match ( "/^select/i", $query )) {
					$eid = mysql_query ( "EXPLAIN $query", self::$db_connect_id );
					CGlobal::$query_debug .= 
					"<tr>
						<td colspan='8' style='background-color:#FFC5Cb'><b>Query :</b> $query</td>
					</tr>
					<tr bgcolor='#edeceb'>
						<td><b>Table</b></td>
						<td><b>Type</b></td>
						<td><b>Possible keys</b></td>
						<td><b>Key</b></td>
						<td><b>Key len</b></td>
						<td><b>Ref</b></td>
						<td><b>Rows</b></td>
						<td><b>Extra</b></td>
					</tr>";
					while ( $array = mysql_fetch_array ( $eid ) ) {
						$type_col = '#FFFFFF';
						if ($array ['type'] == 'ref' or $array ['type'] == 'eq_ref' or $array ['type'] == 'const') {
							$type_col = '#D8FFD4';
						} else if ($array ['type'] == 'ALL') {
							$type_col = '#FFEEBA';
						}
						
						CGlobal::$query_debug .= 
						"<tr bgcolor='#FFFFFF'>
						      <td>$array[table]&nbsp;</td>
						      <td bgcolor='$type_col'>$array[type]&nbsp;</td>
						      <td>$array[possible_keys]&nbsp;</td>
						      <td>$array[key]&nbsp;</td>
						      <td>$array[key_len]&nbsp;</td>
						      <td>$array[ref]&nbsp;</td>
						      <td>$array[rows]&nbsp;</td>
						      <td>$array[Extra]&nbsp;</td>
						</tr>\n";
					}
					
					CGlobal::$query_time += $doing_time;
					
					if ($doing_time > 0.1) {
						$doing_time = "<span style='color:red'><b>$doing_time</b></span>";
					}
					
					CGlobal::$query_debug .= 
						"<tr>
							<td colspan='8' bgcolor='#fff'>
								<div id='query-detail-$hash_key'><a href='javascript:void(0)'  onclick='document.getElementById(\"query-$hash_key\").style.display = \"block\";document.getElementById(\"query-detail-$hash_key\").style.display = \"none\";'>More detail...</a></div>
								<div style='display:none' id='query-$hash_key'>$traceText</div>
							</td>
						</tr>
						<tr>
							<td colspan='8' bgcolor='#fff'><b>MySQL time</b>: $doing_time</b></td>
						</tr>";
				} else {
					if ($doing_time > 0.1) {
						$doing_time = "<span style='color:red'><b>$doing_time</b></span>";
					}
					CGlobal::$query_debug .= 
					"<tr>
						<td bgcolor='#edeceb' colspan='8'><b>Non Select Query : </b>$query</td>
					</tr>
					<tr>
						<td bgcolor='#fff' colspan='8'>
							<div id='query-detail-$hash_key'><a href='javascript:void(0)'  onclick='document.getElementById(\"query-$hash_key\").style.display = \"block\";document.getElementById(\"query-detail-$hash_key\").style.display = \"none\";'>More detail...</a></div>
							<div style='display:none' id='query-$hash_key'>$traceText</div>
						</td>
					</tr>
					<tr>
						<td bgcolor='#fff' colspan='8'><b>MySQL time</b>: $doing_time</span></td>
					</tr>";
				}
			}
		}
		
		return self::$db_result;
	}
	
	// function  close
	// Close SQL connection
	// should be called at very end of all scripts
	// ------------------------------------------------------------------------------------------
	static function close($con_id = false) {
		if ($con_id) {
			$result = @mysql_close ( $con_id );
			return $result;
		} else {
			if (isset ( self::$db_result ) && self::$db_result) {
				@mysql_free_result ( self::$db_result );
				self::$db_result = false;
			}
			
			if (isset ( self::$master_connect ) && self::$master_connect) {
				@mysql_close ( self::$master_connect );
				self::$master_connect = false;
			}
		}
		return true;
	}
	
	static function count($table, $condition = false) {
		$sql = 'SELECT COUNT(*) AS total FROM `' . $table . '`' . ($condition ? ' WHERE ' . $condition : '');
		return self::fetch ( $sql, 'total', 0);
	}
	
	//Lay ra ban ghi trong bang $table thoa man dieu kien $condition voi limit $limit va sap xep theo $order
	static function select($table, $condition = false, $limit = false, $order = false) {
		if ($order) {
			$order = ' ORDER BY ' . $order;
		}
		if ($condition) {
			$condition = ' WHERE ' . $condition;
		}
		$sql = "SELECT * FROM $table $condition $order $limit";
		
		self::query ( $sql );
		return self::fetch_all ();
	}
	
	//Tra ve ban ghi query tu CSDL bang lenh SQL $query neu co
	//Neu khong co tra ve false
	//$query: cau lenh SQL se thuc hien
	static function exists($query) {
		self::query ( $query );
		if (self::num_rows () >= 1) {
			return self::fetch ();
		}
		return false;
	}
	
	static function query_debug() {
		return self::$query_debug;
	}
	
	static function insert($table, $values, $replace = false) {
		if ($replace) {
			$query = 'REPLACE';
		} else {
			$query = 'INSERT INTO';
		}
		
		$query .= ' `' . $table . '`(';
		$i = 0;
		if (is_array ( $values )) {
			foreach ( $values as $key => $value ) {
				if (($key === 0) or is_numeric ( $key )) {
					$key = $value;
				}
				if ($key) {
					if ($i != 0) {
						$query .= ',';
					}
					$query .= '`' . $key . '`';
					$i ++;
				}
			}
			$query .= ') VALUES(';
			$i = 0;
			
			foreach ( $values as $key => $value ) {
				if (is_numeric ( $key ) or $key === 0) {
					$value = Url::getParam ( $value );
				}
				
				if ($i != 0) {
					$query .= ',';
				}
				
				if ($value === 'NULL') {
					$query .= 'NULL';
				} else {
					$query .= '\'' . self::escape ( $value ) . '\'';
				}
				$i ++;
			}
			$query .= ')';
			
			if (self::query ( $query)) {
				$id = self::insert_id ();
				return $id;
			}
		}
	}
	
	static function delete($table, $condition) {
		$query = 'DELETE FROM `' . $table . '` WHERE ' . $condition;
		
		if (self::query ( $query)) {
			return true;
		}
		return false;
	}
	
	static function delete_id($table, $id) {
		return self::delete ( $table, 'id="' . addslashes ( $id ) . '"' );
	}
	
	static function update($table, $values, $condition) {
		$query = 'UPDATE `' . $table . '` SET ';
		$i = 0;
		
		if ($values) {
			foreach ( $values as $key => $value ) {
				if ($key === 0 or is_numeric ( $key )) {
					$key = $value;
					$value = Url::getParam ( $value );
				}
				
				if ($i != 0) {
					$query .= ',';
				}
				
				if ($key) {
					if ($value === 'NULL') {
						$query .= '`' . $key . '`=NULL';
					} else {
						$query .= '`' . $key . '`=\'' . self::escape ( $value ) . '\'';
					}
					$i ++;
				}
			}
			$query .= ' WHERE ' . $condition;
			if (self::query ( $query)) {
				return true;
			}
		}
		return false;
	}
	
	static function update_id($table, $values, $id) {
		return self::update ( $table, $values, 'id="' . $id . '"' );
	}
	
	static function num_rows($query_id = 0) {
		if (! $query_id) {
			$query_id = self::$db_result;
		}
		
		if ($query_id) {
			$result = @mysql_num_rows ( $query_id );
			
			return $result;
		} else {
			return false;
		}
	}
	
	static function affected_rows() {
		if (isset ( self::$db_connect_id ) and self::$db_connect_id) {
			$result = @mysql_affected_rows ( self::$db_connect_id );
			
			return $result;
		} else {
			return false;
		}
	}
	
	
	static function fetch_row($query_id = "") {
		
		if ($query_id == "") {
			$query_id = self::$db_result;
		}
		
		$record_row = mysql_fetch_array ( $query_id, MYSQL_ASSOC );
		return $record_row;
	}
	
	static function fetch($sql = false, $field = false, $default = false) {
		if ($sql) {
			self::query ( $sql);
		}
		
		$query_id = self::$db_result;
		if ($query_id) {
			if ($result = @mysql_fetch_assoc ( $query_id )) {
				if ($field && isset ( $result [$field] )) {
					return $result [$field];
				} elseif ($default !== false) {
					return $default;
				}
				return $result;
			} elseif ($default !== false) {
				return $default;
			}
			return $default;
		} else {
			return false;
		}
	}
	
	static function fetch_all($sql = false) {
		if ($sql) {
			self::query ( $sql );
		}
		$query_id = self::$db_result;
		
		if ($query_id) {
			$result = array ();
			while ( $row = @mysql_fetch_assoc ( $query_id ) ) {
				if (isset ( $row ['id'] ))
					$result [$row ['id']] = $row;
				else
					$result [] = $row;
			}
			
			return $result;
		} else {
			return false;
		}
	}
		
	static function insert_id() {
		if (self::$db_connect_id) {
			$result = mysql_insert_id ( self::$db_connect_id );
			return $result;
		} else {
			return false;
		}
	}
	
	static function escape($sql) {
		return addslashes ( $sql );
	}
	
	static function num_queries() {
		return self::$db_num_queries;
	}
	
	static function import($file = '', &$msg = ''){
		$templine = '';
		$lines = file($file);
		foreach ($lines as $line){
			if (substr($line, 0, 2) == '--' || $line == '')	continue;
			$templine .= $line;
			if (substr(trim($line), -1, 1) == ';'){
				if(DB::query($templine)){
					$templine = '';
				}else{
					$msg = 'Lỗi dòng ' . $templine . ': ' . mysql_error();
					return false;
				}
			}
		}
		return true;
	}
	
	static function export($dir = '', &$backup_name='', $tables=false){
		$res = DB::query('SHOW TABLES'); 
        while($row = mysql_fetch_row($res)){
			$target_tables[] = $row[0];
        }   
        if($tables !== false){ 
            $target_tables = array_intersect( $target_tables, $tables); 
        }
        foreach($target_tables as $table){
			//khong sao luu bang backup
			if(strpos($table, 'backup')!== false){
				continue;
			}
            $result         =   DB::query('SELECT * FROM '.$table);
            $fields_amount  =   mysql_num_fields($result); //so tuong
			$rows_num		=	DB::count($table);

            $res            =   DB::query('SHOW CREATE TABLE '.$table); 
            $TableMLine     =   mysql_fetch_row($res);
			$content        = 	(!isset($content) ?  '' : $content) . "\n\nDROP TABLE IF EXISTS `$table`;\n".$TableMLine[1].";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) 
            {
                while($row = mysql_fetch_row($result))  
                { //when started (and every after 100 command cycle):
                    if ($st_counter%100 == 0 || $st_counter == 0 )  
                    {
                        $content .= "\nINSERT INTO ".$table." VALUES";
                    }
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++)  
                    { 
                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); 
                        if (isset($row[$j]))
                        {
                            $content .= '"'.$row[$j].'"' ; 
                        }
                        else 
                        {   
                            $content .= '""';
                        }     
                        if ($j<($fields_amount-1))
                        {
                            $content.= ',';
                        }      
                    }
                    $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {   
                        $content .= ";";
                    } else {
                        $content .= ",";
                    } 
                    $st_counter=$st_counter+1;
                }
            }
			$content .="\n\n\n";
        }
		$name = 'backup';
		$backup_name = $backup_name != '' ? $backup_name : $name."___".date('H-i-s')."_".date('d-m-Y').".sql";

		if($dir == ''){
			header('Content-Type: application/octet-stream');   
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"".$backup_name."\"");
			echo $content; exit;
		}else{
			$tmp_dir = ROOT_PATH.IMAGE_PATH_STATIC.'tmp/';
			FileHandler::CheckDir($tmp_dir);
			$handle = @fopen($tmp_dir.$backup_name, "w");
			if ($handle) {
				fwrite($handle, $content);
				fclose($handle);
			}
			if(FileHandler::upload($tmp_dir.$backup_name, $dir.$backup_name, true)){
				FileHandler::delete('tmp/'.$backup_name);
				return true;
			}else{
				FileHandler::delete('tmp/'.$backup_name);
			}
		}
		return false;
	}
}