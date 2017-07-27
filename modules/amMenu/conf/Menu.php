<?php

class Menu{
	static $key = 'site_menu';

	static function getMenu($type = 0){
		if($type <= 0){
			$menuArr = CGlobal::get("menu_db", array());
			if(empty($menuArr)){
				$menuArr = ConfigSite::getConfigFromDB(self::$key, array(), true);
				CGlobal::set("menu_db", $menuArr);
			}
			return $menuArr;
		}else{
			$menu = CGlobal::get("menu", array());
			if(empty($menu)){
				$menu = ConfigSite::getConfigFromDB(self::$key, array(), true);
				self::fetchMenu($menu);
				CGlobal::set("menu", $menu);
			}
			if($type > 0){
				$arrType = self::getMenuType();
				return (isset($arrType[$type]) && isset($menu[$type])) ? $menu[$type] : array();
			}
			return $menu;
		}
		return false;
	}
	
	static function fetchMenu(&$menu = array()){
		if(!empty($menu)){
			$arr = array();
			foreach($menu as $k => $v){
				$arr[$v['position']][$k] = $v;
			}
			$menu = $arr;
		}
		return $menu;
	}
	
	static function getMenuItem($id = 0, $del = false){
		$menuArr = self::getMenu();
		if($id > 0 && !empty($menuArr)){
			foreach($menuArr as $menu_id => $menu){
				if($menu_id == $id){
					if($del){
						unset($menuArr[$menu_id]);
						CGlobal::set("menu_db", $menuArr);
					}
					return $menu;
				}
				if(isset($menu['items']) && !empty($menu['items'])){
					foreach($menu['items'] as $sub_id => $sub_menu){
						if($sub_id == $id){
							if($del){
								unset($menuArr[$menu_id]['items'][$sub_id]);
								CGlobal::set("menu_db", $menuArr);
							}
							return $sub_menu;
						}
					}
				}
			}
		}
		return false;
	}
	
	static function removeMenu($id = 0){
		$menu = array();
		if($id > 0){
			$menu = self::getMenuItem($id,true);
		}
		if(!empty($menu)){
			$menuArr = Menu::getMenu();
			return self::saveToDB($menuArr);
		}
		return false;
	}
	
	static function saveToDB($menuArr = array()){
		if(!empty($menuArr)){
			ConfigSite::setConfigToDB(self::$key, serialize($menuArr));
			ConfigSite::clearCacheConfig();//xoa cache config
			return true;
		}
		return false;
	}
	
	
	static function createMenu($title = '', $link = '', $type = 4, $parent = 0, $per = '', $id = 0, $icon = '', $weight = 0, $no_follow = 1, $target = 0, $link_type = 0){
		$menu = array(
			'id' => $id > 0 ? $id : TIME_NOW,
			'title' => $title,
			'link'  => $link,
			'type'  => $link_type,
			'weight'=> $weight,
			'icon'  => $icon,
			'per'	=> $per,
			'no_follow' => $no_follow,
			'target'	=> $target,
			'position'	=> $type,
			'parent'	=> $parent
		);
		return Menu::addMenu($menu);
	}
	
	static function addMenu($menu = array(), $save = true){
		if(!empty($menu)){
			$menuArr = self::getMenu();
			if($menu['parent'] == 0){
				$menuArr = self::addMenuByWeight($menuArr, $menu);
			}else{
				$notFound = true;
				foreach($menuArr as $menu_id => $menuItem){
					if($menu_id == $menu['parent']){
						if(isset($menuArr[$menu_id]['items']) && !empty($menuArr[$menu_id]['items'])){
							$menuArr[$menu_id]['items'] = self::addMenuByWeight($menuArr[$menu_id]['items'], $menu);
						}else{
							$menuArr[$menu_id]['items'][$menu['id']] = $menu;
						}
						$notFound = false;
					}
				}
				if($notFound){
					return false;
				}
			}
			if($save && !empty($menuArr)){
				//System::debug($menuArr);
				return self::saveToDB($menuArr);
			}
			return true;
		}
		return false;
	}
	
	static function addMenuByWeight($menuArr = array(), $new_item = array()){
		if(!empty($menuArr) && !empty($new_item)){
			$newArr = array();
			foreach($menuArr as $k => $menu){
				if($menu['weight'] >= $new_item['weight']){
					$newArr[$new_item['id']] = $new_item;
					$new_item['inserted'] = true;
				}
				$newArr[$k] = $menu;
			}
			if(isset($new_item['inserted'])){
				return $newArr;
			}
			$menuArr[$new_item['id']] = $new_item;
			return $menuArr;
		}
		return false;
	}
	
	static function getMenuType(){
		return array(1 => 'Menu Header', 2 => 'Menu Footer', 4 => 'Menu Hệ thống', 3 => 'Menu Khác');
	}
	
	static function parentMenuOptions($parent = 0, $mid = 0){
		$opts = '<option value="0"> -- Chọn menu -- </option>';
		$menu = self::getMenu();
		$menu = self::fetchMenu($menu);
		if(!empty($menu)){
			$type = self::getMenuType();
			foreach($menu as $k => $m){
				$tempMenu = '';
				foreach($m as $id => $item){
					if($mid != $id){
						$tempMenu .= '<option value="'.$id.'"'.($parent == $id ? ' selected':'').'> &nbsp;&nbsp;'.$item['title'].'</option>';
					}
				}
				if($tempMenu != ''){
					$opts .= '<optgroup label="'.$type[$k].'">'.$tempMenu.'</optgroup>';
				}
			}
		}
		return $opts;
	}
	
	static function autoEdit(&$form, &$data = array(), $action = ''){
		if(!empty($form)){
			$form->layout->init(array(
				'style'		=>	'edit',
				'method'	=>	'POST',
				'onsubmit'	=>	'shop.admin.menu.onSubmit'
			));
	
			//add group
			$form->layout->addGroup('main', array('title' => 'Thông tin cơ bản'));
			$form->layout->addGroup('sys', array('title' => 'Dành cho hệ thống'));
	
			//add form item by Group main
			$form->layout->addItem('id', array(
				'type'	=> 'hidden',
				'value' => $form->id,
				'save'  => false
			), 'main');
			$form->layout->addItem('title', array(
				'type'	=> 'text',
				'title' => 'Tên menu',
				'value' => Url::getParam('title', $form->item['title']),
				'required' => true
			), 'main');
			$form->layout->addItem('link', array(
				'type'	=> 'text',
				'title' => 'URL',
				'value' => Url::getParam('link', $form->item['link'])
			), 'main');
			$type = array('0' => 'Liên kết trong site', '1' => 'Liên kết bên ngoài site');
			$form->layout->addItem('type', array(
				'number'=> true,
				'type'	=> 'select',
				'title' => 'Kiểu liên kết',
				'options' => FunctionLib::getOption($type, Url::getParamInt('type', $form->item['type']))
			), 'main');
			$form->layout->addItem('weight', array(
				'number'=> true,
				'type'	=> 'text',
				'title' => 'Sắp xếp',
				'value' => Url::getParamInt('weight',$form->item['weight']),
				'ext' => array(
					'onkeypress' => 'return shop.numberOnly(this, event)',
					'maxlength'  => 10
				)
			), 'main');
			$form->layout->addItem('old_weight', array(
				'type'	=> 'hidden',
				'value' => $form->item['weight'],
				'save'  => false
			), 'main');
			$form->layout->addItem('no_follow', array(
				'number'=> true,
				'type'	=> 'checkbox',
				'style' => 'onoff',
				'title' => 'No Follow',
				'checked' => Url::getParamInt('no_follow', $form->item['no_follow']) == 1,
			), 'main');
			$form->layout->addItem('target', array(
				'number'=> true,
				'type'	=> 'checkbox',
				'style' => 'onoff',
				'title' => 'Bật tab mới',
				'checked' => Url::getParamInt('target', isset($form->item['target']) ? $form->item['target'] : 0) == 1,
			), 'main');
			$form->layout->addItem('position', array(
				'number'=> true,
				'type'	=> 'select',
				'title' => 'Loại Menu',
				'options' => FunctionLib::getOption(Menu::getMenuType(), $form->item['position'])
			), 'main');
			$form->layout->addItem('parent', array(
				'number'=> true,
				'type'	=> 'select',
				'title' => 'Menu cha',
				'options' => self::parentMenuOptions($form->item['parent'], $form->id)
			), 'main');
			$form->layout->addItem('old_parent', array(
				'type'	=> 'hidden',
				'value' => $form->item['parent'],
				'save'  => false
			), 'main');
			
			$permission = '<option value="">Không quyền hạn</option>';
			foreach(CGlobal::$permission as $name => $modulePer){
				$permission .= '<optgroup label="'.$name.'">';
				foreach($modulePer as $k => $v){
					$permission .= '<option value="'.$k.'"'.($form->item['position']==4 && $form->item['per']==$k?' selected':'').'>'.$v.'</option>';
				}
				$permission .= '</optgroup>';
			}
			$form->layout->addItem('per', array(
				'type'	=> 'select',
				'title' => 'Quyền hạn',
				'options' => $permission
			), 'sys');
			
			$form->layout->addItem('icon', array(
				'type'	=> 'text',
				'title' => 'Biểu tượng',
				'value' => Url::getParam('icon', isset($form->item['icon']) ? $form->item['icon'] : ''),
			), 'sys');
			
			if($action == 'draw'){
				return $form->layout->genFormAuto($form, $data);
			}elseif($action == 'submit'){
				return $form->auto_submit($data);
			}
		}
		return false;
	}
}