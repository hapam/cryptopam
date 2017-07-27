<?php
class sysLayout extends Module{
	function __construct($row){
		Module::Module($row);

		if(User::is_root()){
			$id	= Url::getParamInt('id',0);
			$block_id = Url::getParamInt('block_id', 0);
			$cmd = Url::getParamAdmin('cmd','');
			$mobile = Url::getParamInt('mobile', 0);
			$not_do = true;
			$del_cache = true;
			switch ($cmd){
				case 'change_layout':case 'change_layout_mobile':
					$new_layout = Url::getParam('new_layout');
					if($id && $new_layout){
						if($cmd == 'change_layout'){
							DB::update(T_PAGE,array('layout' => $new_layout), 'id='.$id);
						}else{
							DB::update(T_PAGE,array('layout_mobile' => $new_layout), 'id='.$id);
						}
					}else{
						$del_cache = false;
					}
					break;
				case 'delete_block':
					if($block_id > 0){
						DB::delete(T_BLOCK, "id=$block_id");
					}else{
						$del_cache = false;
					}
					break;
				case 'delete_all_block':
					$ids = Url::getParam('ids','');
					if($ids != ''){
						$ids = str_replace('|', ',', $ids);
						DB::delete(T_BLOCK, "id IN ($ids)");
					}else{
						$del_cache = false;
					}
					break;
				case 'move_bottom':
				case 'move_top':
					$this->moveTopBottom($cmd, $id, $block_id, $mobile);
				break;
				case 'move':
					$this->move($id, $block_id, $mobile);
				break;
				default:
					$module_id	= Url::getParamInt('module_id',0);
					$region		= Url::getParam('region','');
					$mobile		= Url::getParamInt('mobile',0);
					if($id <= 0){
						Url::redirect('page');
					}
					if($module_id > 0 && $region != ''){
						$position = DB::fetch('SELECT MAX(position) AS amax FROM '.T_BLOCK.' WHERE region="'.$region.'" AND page_id="'.$id.'"','amax',0);
						if($position){
							$position++;
						}
						if($position <= 0){
							$position = 1;
						}
						DB::insert(T_BLOCK, array(
							'region'=>$region,
							'position'=>$position,
							'page_id'=>$id,
							'module_id'=>$module_id,
							'mobile' => $mobile
						));
						unset($_SESSION['assign_mode']);
					}
					else{//Cấu hình page:
						require_once 'forms/page_content.php';
						$this->add_form(new PageContentForm());
						$not_do = false;
					}
			}
			if($not_do){
				//xoa cache
				if($del_cache){
					Layout::update_page($id);
				}
				//chuyen trang
				if(Url::check('href') && Url::getParam('href')){
					Url::redirect_url(Url::getParam('href'));
				}
				else{
					Url::redirect_current(array('id'=>$id));
				}
			}
		}
		else{
			Url::access_denied();
		}
	}
	
	function move($id = 0, $block_id = 0, $mobile = 0){
		$dir = Url::getParam('move','');
		if($id > 0 && $block_id > 0 && $dir != ''){
			$block = DB::select(T_BLOCK, "id=$block_id");
			if($block){
				$block = $block[$block_id];
				$move[0] = ($dir=='up') ? '<' : '>';
				$move[1] = ($dir=='up') ? 'DESC' : 'ASC';
				$sql = 'SELECT * FROM '.T_BLOCK.'
						WHERE region = "'.DB::escape($block['region']).'"
							AND page_id = "'.$block['page_id'].'"
							AND position '.$move[0].$block['position'].'
							AND mobile = '.$mobile.'
						ORDER BY position '.$move[1];
				$res = DB::query($sql);
				if($row = mysql_fetch_assoc($res)){
					DB::update(T_BLOCK, array('position' => $block['position']), '`id`='.$row['id']);
					DB::update(T_BLOCK, array('position' => $row['position']), '`id`='.$block['id']);
				}
			}
		}
	}
	
	function moveTopBottom($cmd = 'move_top', $id = 0, $block_id = 0, $mobile = 0){
		if($block_id > 0 && $id > 0){
			$page  = DB::select(T_PAGE,  'id = '.$id);
			$block = DB::select(T_BLOCK, 'id = '.$block_id);
			if($block && $page){
				$page  = $page[$id];
				$block = $block[$block_id];
				$region= $block['region'];
				if($cmd == 'move_bottom'){
					echo $position = DB::fetch('SELECT MAX(position) AS amax FROM '.T_BLOCK.' WHERE region="'.$region.'" AND page_id="'.$id.'" AND mobile = '.$mobile,'amax',0);
					if($position){
						$position++;
					}
					else{
						$position = 1;
					}
				}
				else{
					$position = DB::fetch('SELECT MIN(position) AS amin FROM '.T_BLOCK.' WHERE region="'.$region.'" AND page_id="'.$id.'" AND mobile = '.$mobile,'amin',0);
					if($position){
						$position--;
					}
					else{
						$position = 1;
					}
				}
				DB::update(T_BLOCK, array('region' => $region, 'position' => $position), 'id="'.$block_id.'"');
			}
		}
	}
}

