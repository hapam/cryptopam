<?php
class PanelForm extends Form{
	var $menu, $title_module;
	function __construct(){
		global $global_menu, $global_title_module;
		
		$this->region = 'header';
        if(User::id() > 0){
			$this->menu = $global_menu;
			$this->title_module = $global_title_module;
			if(!$global_menu){
				$this->menu = $this->getMenu();
				
				//create breadcum
				FunctionLib::addBreadcrumb('Admin', Url::buildAdminURL('admin'), false);
				if(CGlobal::$current_page == 'admin'){
					$cmd = Url::getParamAdmin('cmd');
					$action = Url::getParamAdmin('action');
				}else{
					$cmd = CGlobal::$current_page;
					$action = isset(CGlobal::$urlArgs[1]) ? CGlobal::$urlArgs[1] : '';
				}
				foreach($this->menu as $k => $v){
					$done = false;
					if(!empty($v['sub'])){
						foreach($v['sub'] as $url => $m){
							if($cmd == $m['key']){
								FunctionLib::addBreadcrumb($v['title'], $v['link']);
								FunctionLib::addBreadcrumb($m['title'], !empty($action) ? $m['link'] : '');
								$this->title_module = $m['title'];
								$done = true;
								break;
							}
						}
					}
					if($done) break;
				}
				$this->breadcumAction($action);

				//luu bien global
				$global_menu = $this->menu;
				$global_title_module = $this->title_module;
				CGlobal::$website_title = ($this->title_module != '' ? $this->title_module : 'Admin').' | '.CGlobal::$site_name;
			}
		}
		
		$this->link_js('modules/sysUser/js/admin_user.js');
	}

	function draw(){
		global $display;

		$menuConfig = @unserialize(CGlobal::$configs['admin_config']);
        $allConfig = ConfigSite::getConfigFromDB('site_configs', '', true);

		if(User::id()>0){
			$display->add('breadcum', FunctionLib::getAdminBreadcrumb());
			$display->add('title_module', $this->title_module);
			$display->add('myIcon', $this->getIcons());
			$display->add('admin_user', $this->getUserInfo());
			$display->add('admin_menu', $this->menu);
		}
        $display->add('admin_menu_config', $menuConfig);
        $display->add('all_config_sites', $allConfig);
        $display->add('logo', CGlobal::$logo);
		$display->add('admin_url', Url::build('admin'));

		$display->output('Panel');
	}
	
	function getMenu(){
		if(User::user_access('access admin page')){
			$page = CGlobal::$current_page;
			$is_root  = User::is_root();

            $specialPage = array('view-code', 'page', 'module', 'themes', 'code', 'mem_reset.php', 'delcache.php');
            $menuArr = Menu::getMenu(4);
            $menu = array();
            foreach($menuArr as $m){
                $sub = array();
                //create sub
                if(isset($m['items']) && !empty($m['items'])){
                    foreach($m['items'] as $s){
						$s['link_full'] = $s['link'];
						$s['link'] = $this->getLinkStandard($s['link']);
                        $key =  basename($s['link'],'.html');
                        $per = false;
                        if(in_array($key, $specialPage)){
                            $per = $is_root;
                        }elseif($s['per'] != ''){
                            $per = User::user_access($s['per']);
                        }
                        $sub[$key.rand()] = array(
							'key' => $key,
                            'per' => $per,
                            'title' => $s['title'],
							'type' => $s['type'],
                            'link' => isset($s['link_full']) ? $s['link_full'] : $s['link'],
							'stand_link' => $s['link'],
							'new_page' => isset($s['target']) && ($s['target'] == 1)
                        );
                    }
                }
                $menu[] = array(
                    'title' => $m['title'],
                    'link' => $m['link'],
                    'icon' => $m['icon'],
					'type' => $m['type'],
                    'sub' => $sub
                );
            }
			return $this->createMenu($menu);
		}
		return false;
	}
	
	function getLinkStandard($curLink = '', $key = '?'){
		$findTail = strpos($curLink, $key);
		if($findTail > 0){
			return substr($curLink, 0, $findTail);
		}
		return $curLink;
	}
	
	function createMenu($menu = array()){
		if(!empty($menu)){
			$cmd  = Url::getParamAdmin('cmd','');
			$action = Url::getParamAdmin('action','');
			//$curLink = $this->getLinkStandard(REQUEST_SCHEME.'://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

			foreach($menu as $idx => $m){
				if(isset($m['link']) && $m['link'] != ''){
					$menu[$idx]['link'] = $m['type'] == 0 ? WEB_ROOT.$m['link'] : $m['link'];
				}else{
					$menu[$idx]['link'] = 'javascript:void(0)';
				}
				if(isset($m['sub']) && !empty($m['sub'])){
					$tempSub = array();
					foreach($m['sub'] as $skey => $sub){
						//tinh toan xem co active khong
						if($sub['type'] == 0){
							if(!isset($sub['active'])){
								if(CGlobal::$current_page == 'admin'){
									$sub['active'] = $cmd == $sub['key'];
									if(!$sub['active']){
										$adminURL = $this->getLinkStandard($sub['stand_link'], 'admin');
										$adminURL = explode('/', str_replace('.html', '', $adminURL));
										if(count($adminURL) > 1){
											$sub['active'] = $cmd == $adminURL[1];
											if(isset($adminURL[2])){
												$sub['active'] = $action == $adminURL[2];
											}
										}
									}
								}else{
									$sub['active'] = CGlobal::$current_page == $sub['key'];
								}
							}
							if($sub['active']){
								$menu[$idx]['active'] = true;
							}
						}
						//kiem tra xem co quyen khong
						if($sub['per']) {
							if(isset($sub['link']) && $sub['link'] != ''){
								$sub['link'] = $sub['type'] == 0 ? WEB_ROOT.$sub['link'] : $sub['link'];
							}else{
								$sub['link'] = 'javascript:void(0)';
							}
							if(isset($sub['space']) && $sub['space'] && !empty($tempSub)){
								$tempSub[$skey] = array('type' => 'space');
							}
							$tempSub[$skey] = $sub;
						}
					}
					if(!empty($tempSub)){
						$menu[$idx]['sub'] = $tempSub;
					}else{
						unset($menu[$idx]);
					}
                }else{
					$menu[$idx]['active'] = false;
					if(isset($m['key'])){
						if(CGlobal::$current_page == 'admin'){
							$menu[$idx]['active'] = $cmd == $m['key'];
						}else{
							$menu[$idx]['active'] = CGlobal::$current_page == $m['key'];
						}
					}
                    if(isset($m['per']) && !$m['per']){
                        unset($menu[$idx]);
                    }
                }
			}
		}
		return $menu;
	}
	
	function getUserInfo(){
		$loginAs = CookieLib::get_cookie('loginAs');
		$loginUser = CookieLib::get_cookie('loginUser');
		return array(
			'name' => User::$current->data['username'],
			'email' => User::$current->data['email'],
			'login_as' => ((User::$current->data['username'] == $loginAs) && ($loginAs != $loginUser) ? 1 : 0),
			'login_user' => $loginUser,
			'admin_home' => Url::buildAdminURL('admin'),
			'image' => FunctionLib::get_gravatar(User::$current->data['email'], 75),
			'logout' => Url::buildAdminURL('sign_out'),
			'change_pass' => Url::buildAdminURL('admin', array('cmd' => 'user', 'action' => 'edit', 'id' => User::id()))
		);
	}
	
	function getIcons(){
		$path = 'style/images/admin/icons/';
		$path2 = 'style/images/admin/';
		return array(
			'add' => $path.'add.png',
			'add2' => $path.'picture_add.png',
			'add_detail' => $path.'detail.png',
			'add_price' => $path.'money.png',
			'del' => $path.'delete.png',
			'edit' => $path.'edit.png',
			'config' => $path.'cog.png',
			'active' => $path.'ok.png',
			'not_active' => $path.'ok_grey.png',
			'log' => $path.'log.png',
			'lock' => $path.'lock.png',
			'online' => $path.'son.png',
			'offline' => $path.'soff.png',
			'tag_add' => $path.'tag_add.png',
			'tick' => $path.'tick.png',
			'perm' => $path.'perm_add.png',
			'view' => $path.'zoom.png',
			'copy' => $path.'page_copy.png',
			'layout' => $path.'layout.png',
			'layout_add' => $path.'layout_add.png',
			'sub' => $path.'add_menu.png',
			'code' => $path.'html.png',
			'clean_small' => $path.'clean.png',
			'clean' => $path2.'clean.png',
			'install' => $path2.'install.png',
			'uninstall' => $path2.'install2.png',
			'hourglass' => $path.'hourglass.png',
			'handshake' => $path.'handshake.png',
			'shipping' => $path.'ship_done.png',
			'office_payment' => $path.'cart.png',
			'cod_payment' => $path.'house_go.png',
			'email_go' => $path.'email_go.png',
			'restore' => $path.'refresh.png',
			'download' => $path.'MoveBottom.png'
		);
	}
	
	function breadcumAction($action = ''){
		$actionArr = array(
			'add' => 'Thêm mới',
			'edit' => 'Sửa',
			'copy' => 'Copy',
			'permission' => 'Cấu hình'
		);
		if(isset($actionArr[$action])){
			FunctionLib::addBreadcrumb($actionArr[$action]);
		}
	}
}
