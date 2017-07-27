<?php

if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_panel {
	function playme(){
		$code = Url::getParam('code');
		switch( $code ){
			case 'list-moule':
				$this->listModule(); 
				break;
			case 'remove-module':
				$this->removeModule();
				break;
			case 'add-module':
				$this->addModule(); 
				break;
			case 'move-module':
				$this->moveModule(); 
				break;
            case 'admin-config':
                $this->adminConfig();
                break;
			default:
				$this->home();
				break;
		}
	}
	
	function moveModule(){
		if(User::is_root()){
			$block_id = Url::getParamInt('block_id', 0);
			$page_id = Url::getParamInt('page_id', 0);
			$type = Url::getParam('type','');
			if($block_id > 0 && $page_id > 0 && $type != ''){
				switch($type){
					case 'top':case 'bottom':
						$page  = DB::select(T_PAGE,'id='.$page_id);
						$block = DB::select(T_BLOCK,'id='.$block_id);
						if($block && $page){
							$block = $block[$block_id];
							$region	= $block['region'];
							if($type=='bottom'){
								$position = DB::fetch('SELECT MAX(position) AS amax FROM '.T_BLOCK.' WHERE region="'.$region.'" AND page_id="'.$page_id.'"','amax',0);
								if($position)
									$position++;
								else $position = 1;
							}
							else{
								$position= DB::fetch('SELECT MIN(position) AS amin FROM '.T_BLOCK.' WHERE region="'.$region.'" AND page_id="'.$page_id.'"','amin',0);
								if($position)
									$position--;
								else $position = 1;
							}
							DB::update(T_BLOCK, array('region'=>$region,'position'=>$position), 'id="'.$block_id.'"');
							Layout::update_page($page_id);
							echo FunctionLib::JsonSuccess('done');
						}else{
							echo FunctionLib::JsonErr('Dữ liệu không hợp lệ');
						}
					break;
					default:
						$block = DB::select(T_BLOCK,'id='.$block_id);
						if($block){
							$block=$block[$block_id];
							if($type=='up'){
								$move[0]='<';
								$move[1]='DESC';
							}
							else{
								$move[0]='>';
								$move[1]='ASC';
							}
							$sql = 'SELECT * FROM '.T_BLOCK.'
									WHERE region="'.DB::escape($block['region']).'"
										AND page_id="'.$block['page_id'].'"
										AND position'.$move[0].$block['position'].'
									ORDER BY position '.$move[1];
							$res = DB::query($sql);
							if($row = mysql_fetch_assoc($res)){
								DB::update(T_BLOCK,array('position'=>$block['position']),'`id`='.$row['id']);
								DB::update(T_BLOCK,array('position'=>$row['position']),'`id`='.$block['id']);
							}
							Layout::update_page($page_id);
							echo FunctionLib::JsonSuccess('done');
						}else{
							echo FunctionLib::JsonErr('Dữ liệu không hợp lệ');
						}
				}
			}else{
				echo FunctionLib::JsonErr('Dữ liệu không hợp lệ');
			}
		}else{
			echo FunctionLib::JsonErr('Truy cập bị từ chối');
		}
	}
	
	function addModule(){
		if(User::is_root()){
			$block_id = Url::getParamInt('block_id', 0);
			$page_id = Url::getParamInt('page_id', 0);
			$region = Url::getParam('region');
			if($block_id > 0 && $page_id > 0 && $region != ''){
				$position=DB::fetch('SELECT MAX(position) AS amax FROM '.T_BLOCK.' WHERE region="'.$region.'" AND page_id="'.$page_id.'"','amax',0);
				if($position){
					$position++;
				}
				if($position<=0){
					$position=1;
				}
				
				DB::insert(T_BLOCK, array(
					'region'	=>	$region,
					'position'	=>	$position,
					'page_id'	=>	$page_id,
					'module_id'	=>	$block_id));
				Layout::update_page($page_id);

				echo FunctionLib::JsonSuccess('done');
			}else{
				echo FunctionLib::JsonErr('Dữ liệu không hợp lệ');
			}
		}else{
			echo FunctionLib::JsonErr('Truy cập bị từ chối');
		}
	}
	
	function removeModule(){
		if(User::is_root()){
			$block_id = Url::getParamInt('block_id', 0);
			$page_id = Url::getParamInt('page_id', 0);
			if($block_id > 0 && $page_id > 0){
				DB::delete(T_BLOCK, "id=$block_id");
				Layout::update_page($page_id);
				echo FunctionLib::JsonSuccess('done');
			}else{
				echo FunctionLib::JsonErr('Dữ liệu không hợp lệ');
			}
		}else{
			echo FunctionLib::JsonErr('Truy cập bị từ chối');
		}
	}
	
	function listModule(){
		if(User::is_root()){
			$items = array();
			$re = DB::query('SELECT id,name FROM '.T_MODULE.' ORDER BY id DESC');
			if($re){
				while ($row = mysql_fetch_assoc($re)){
					$row['pages']=array();
					$re2 = DB::query('SELECT p.id,p.name FROM '.T_BLOCK.' b INNER JOIN '.T_PAGE.' p ON p.id=b.page_id WHERE module_id="'.$row['id'].'"');
					if($re2){
						while ($page = mysql_fetch_assoc($re2)){
							$row['pages'][$page['id']]=$page;
						}
					}
					$items[$row['id']] = $row;
				}
			}
			echo FunctionLib::JsonSuccess('done', array('data' => $items));
		}else{
			echo FunctionLib::JsonErr('Truy cập bị từ chối');
		}
	}

    function adminConfig(){
        if(User::is_admin()){
            $type = Url::getParam('type', '');
            $value = Url::getParam('value', '');
            if($type != '' && $value != ''){
                $key = 'admin_config';
                $allValues = ConfigSite::getConfigFromDB($key,array(),true);
                $allValues[$type] = $value;
                ConfigSite::setConfigToDB($key, serialize($allValues));

                if($type == 'menu'){
                    $layout = array('ngang' => 'layouts/admin_menu_ngang.html', 'doc' => 'layouts/admin.html');
                    DB::update(T_PAGE,array('layout' => $layout[$value]), 'id IN (1,3,4,21)'); //1:page, 3:module, 4:admin, 21:theme
                    //xoa cache page
                    Layout::update_all_page();
					//xoa cache
					if(MEMCACHE_ON){
						memcacheLib::clear();
					}
                }
                //xoa cache config
				ConfigSite::clearCacheConfig();

                echo FunctionLib::JsonSuccess('done');
            }else{
                echo FunctionLib::JsonErr('Dữ liệu không hợp lệ');
            }
        }else{
            echo FunctionLib::JsonErr('Truy cập bị từ chối');
        }
    }
	
	function home(){die("Nothing to do...");}
}