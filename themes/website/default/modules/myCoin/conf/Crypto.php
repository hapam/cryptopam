<?php
class Crypto{
	static function autoList(&$form, $data = array()){
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET'
		));
		//add group search
		$form->layout->addGroup('main', array('title' => 'Thông tin'));
		$form->layout->addGroup('time', array('title' => 'Thời gian'));
		//add item to search
		$form->layout->addItem('name', array(
			'type'	=> 'text',
			'title' => 'Tên Coin'
		), 'main');
		$form->layout->addItem('title', array(
			'type'	=> 'text',
			'title' => 'Mô tả'
		), 'main');
		$form->layout->addItem('created_time', array(
			'type'	=> 'text',
			'title' => 'Ngày tạo từ',
			'time'  => true,
			'holder'=> 'Ext: 30-07-2016'
		), 'time');
		$form->layout->addItem('created_time_to', array(
			'type'	=> 'text',
			'title' => 'Ngày tạo đến',
			'time'  => true,
			'holder'=> 'Ext: 30-07-2016'
		), 'time');
		
		//add item to view
		$form->layout->addItemView('btn-del-check', array(
			'per'	=>	$form->perm['del'],
			'type'	=>	'del',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('name', array(
			'title' => 'Tên Coin',
		));
		$form->layout->addItemView('title', array(
			'title' => 'Mô tả',
		));
		$form->layout->addItemView('created', array(
			'title' => 'Ngày tạo',
			'head' => array(
				'width' => 50			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-edit', array(
			'title' =>	'Sửa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['edit'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-del', array(
			'title' =>	'Xóa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['del'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		
		return $form->layout->genFormAuto($form, $data);
	}

	static function autoEdit(&$form, &$data = array(), $action = ''){
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST'
		));

		//add group
		$form->layout->addGroup('main', array('title' => 'Thông tin cơ bản'));

		//add form item by Group main
		$form->layout->addItem('id', array(
			'type'	=> 'hidden',
			'value' => $form->id,
			'save'  => false
		), 'main');
		$form->layout->addItem('name', array(
			'type'	=> 'text',
			'required' => true,
			'title' => 'Tên Coin',
			'value' => Url::getParam('name', $form->item['name']),
		), 'main');
		$form->layout->addItem('title', array(
			'type'	=> 'text',
			'title' => 'Mô tả',
			'value' => Url::getParam('title', $form->item['title']),
		), 'main');
		if($action == 'draw'){
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
	
	static function updateAllCoin($debug = false){
		//lay ra id coin da store
		$storeCoin = array();
		$res = DB::query("SELECT * FROM ".T_COIN." WHERE status = 1");
		while($r = @mysql_fetch_assoc($res)){
			$storeCoin[$r['name']] = $r['id'];
		}
		//lay ra id pair da store
		$storePair = array();
		$res = DB::query("SELECT * FROM ".T_COIN_PAIR);
		while($r = @mysql_fetch_assoc($res)){
			$storePair[$r['pair']] = $r['id'];
		}
		
		//lay du lieu tu polo
		$polo = new poloniex();
		$tickers = $polo->get_ticker();
		
		$sql = "INSERT INTO ".T_COIN_RATE." (`last`,`lowestAsk`,`highestBid`,`percentChange`,`baseVolume`,`quoteVolume`,`isFrozen`,`high24hr`,`low24hr`,`pair_id`,`created`) VALUES ";
		
		foreach($tickers as $pair => $val){
			$tmpCoins = explode('_', $pair);
			
			//neu chua co DB thi them
			if(!isset($storeCoin[$tmpCoins[0]])){
				$storeCoin[$tmpCoins[0]] = DB::insert(T_COIN, array('name' => $tmpCoins[0], 'created' => TIME_NOW));
			}
			if(!isset($storeCoin[$tmpCoins[1]])){
				$storeCoin[$tmpCoins[1]] = DB::insert(T_COIN, array('name' => $tmpCoins[1], 'created' => TIME_NOW));
			}
			//tao pair
			if(!isset($storePair[$pair])){
				DB::insert(T_COIN_PAIR, array(
					'id' => $val['id'],
					'pair' => $pair,
					'coin_id' => $storeCoin[$tmpCoins[1]],
					'coint_price_id' => $storeCoin[$tmpCoins[0]]
				));
			}
			
			//khoi tao
			if(!isset($coinArr[$tmpCoins[0]])){
				$coinArr[$tmpCoins[0]] = array();
			}
			$val['pair_id'] = $val['id'];
			$val['created'] = TIME_NOW;
			unset($val['id']);
			
			//gan vao mang
			//$tickers[$pair] = $val;
			$tickers[$pair] = "({$val['last']},{$val['lowestAsk']},{$val['highestBid']},{$val['percentChange']},{$val['baseVolume']},{$val['quoteVolume']},{$val['isFrozen']},{$val['high24hr']},{$val['low24hr']},{$val['pair_id']},{$val['created']})";
			if($debug){
				echo '<p>Update coin '.$pair.': <b>'.$val['last'].'</b></p>';
			}
		}
		$sql .= implode(',', $tickers);
		if($debug){
			echo "<div>$sql</div>";
		}
		
		return DB::query($sql);
	}
	
	static function getTickerFromPolo(){
		$polo = new poloniex();
		$tickers = $polo->get_ticker();
		
		$data = array();
		foreach($tickers as $pair => $val){
			$tmpCoins = explode('_', $pair);
			
			if(!isset($data[$tmpCoins[0]])){
				$data[$tmpCoins[0]] = array(
					'data' => array('name' => $tmpCoins[0]),
					'pairs' => array()
				);
			}
			$val['name'] = $tmpCoins[1];
			$data[$tmpCoins[0]]['pairs'][$tmpCoins[1]] = $val;
		}
		return $data;
	}
	
	static function getTickerFromDB(){
		//lay ra id coin da store
		$storeCoin = array();
		$res = DB::query("SELECT id, name, title FROM ".T_COIN." WHERE status = 1");
		while($r = @mysql_fetch_assoc($res)){
			$storeCoin[$r['id']] = $r;
		}

		//lay ra id pair da store
		$storePair = array();
		$tickers = array();
		$res = DB::query("SELECT * FROM ".T_COIN_PAIR." ORDER BY pair");
		while($r = @mysql_fetch_assoc($res)){
			$storePair[$r['id']] = $r;
			$coin = isset($storeCoin[$r['coint_price_id']]) ? $storeCoin[$r['coint_price_id']] : array();
			if(!empty($coin)){
				if(!isset($tickers[$coin['name']])){
					$tickers[$coin['name']] = array('data' => $coin, 'pairs' => array());
				}
				if(isset($storeCoin[$r['coin_id']])){
					$tickers[$coin['name']]['pairs'][$r['id']] = array(
						'pair' => $r['pair'],
						'name' => $storeCoin[$r['coin_id']]['name'],
						'title' => $storeCoin[$r['coin_id']]['title'],
					);
				}
			}
		}
		
		//lay du lieu trong csdl
		$res = DB::query("SELECT * FROM (SELECT * FROM ".T_COIN_RATE." ORDER BY created DESC) A GROUP BY A.pair_id");
		while($r = @mysql_fetch_assoc($res)){
			if(isset($storePair[$r['pair_id']])){
				$pair = $storePair[$r['pair_id']];
				$coin = isset($storeCoin[$pair['coint_price_id']]) ? $storeCoin[$pair['coint_price_id']] : array();
				if(!empty($coin) && isset($tickers[$coin['name']]) && isset($tickers[$coin['name']]['pairs'][$r['pair_id']])){
					$r['pair'] = $pair['pair'];
					$r['name'] = $tickers[$coin['name']]['pairs'][$r['pair_id']]['name'];
					$r['title'] = $tickers[$coin['name']]['pairs'][$r['pair_id']]['title'];
					$tickers[$coin['name']]['pairs'][$r['pair_id']] = $r;
				}
			}
		}
		
		return $tickers;
	}
	
	static function getPairsByUser($uid = 0){
		if($uid == 0){
			$uid = User::id();
		}
		$default = array();
        $res = DB::query("SELECT * FROM ".T_COIN_USER." WHERE uid = $uid");
        while($r = @mysql_fetch_assoc($res)){
            $default[$r['pair_id']] = $r;
        }
		return $default;
	}
	static function removeDataRate(){
		$day = ConfigSite::getConfigFromDB('time_store',1,false,'module_configs');
		$time= TIME_NOW - $day*86400;
		
		DB::delete(T_COIN_RATE, "created <= $time");
	}
}