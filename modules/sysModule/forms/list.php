<?php
class ListModuleAdminForm extends Form{
	var $modules, $allInfo, $installed, $normal, $core, $admin, $notAssign, $notInstalled, $idByName, $nameByID, $opt, $removed, $mobile, $assign_mode;
	function __construct(){
		parent::__construct();
		$this->assign_mode = Url::check('page_id');
		if($this->assign_mode && !isset($_SESSION['assign_mode'])){
			$_SESSION['assign_mode'] = array(
				'id' => Url::getParamInt('page_id',0),
				'region' => Url::getParam('region'),
				'mobile' => Url::getParam('mobile')
			);
		}
		
		$cmd = Url::getParamAdmin('cmd', '');
		$this->init();
		if($cmd == 'delete_cache'){
			$this->cleanModuleList();
		}elseif($cmd == 'cancel_page'){
			unset($_SESSION['assign_mode']);
			Url::redirect('module');
		}
		$this->link_js_me('admin_module.js', __FILE__);
	}
	
	function draw(){
		if(isset($_SESSION['success_alert']) && $_SESSION['success_alert'] != ''){
			$this->setFormSucces('', $_SESSION['success_alert']);
			$_SESSION['success_alert'] = '';
		}
		if(isset($_SESSION['assign_mode']) && !empty($_SESSION['assign_mode'])){
			$page = DB::fetch("SELECT * FROM ".T_PAGE." WHERE id = ".$_SESSION['assign_mode']['id']);
			if(!empty($page)){
				$this->setFormSucces('page', 'Đang trong chế độ chọn Module để gán vào vùng <b>'.$_SESSION['assign_mode']['region'].'</b> của trang <b>'.$page['name'].'</b>. <a href="'.Url::build('module', array('cmd' => 'cancel_page')).'" class="alert-link">Bấm vào đây</a> để hủy');
			}
		}

		$title = array(
			'not_assign' => 'Chưa gán',
			'core' => 'Core',
			'admin' => 'Admin',
			'normal' => 'Public',
            'mobile' => 'Mobile',
			'removed' => 'Removed or Not Actived'
		);
		$data = array(
			'notInstalled' => $this->getMoreInfo($this->notInstalled),
			'installed' => array(
				'not_assign' => $this->notAssign,
				'core' => $this->core,
				'admin' => $this->admin,
				'normal' => $this->normal,
                'mobile' => $this->mobile,
				'removed' => $this->removed
			),
		);
		$stitle = Url::getParam('title');
		$type = Url::getParamInt('module_type', 0);
		if($type > 0){
			switch($type){
				case 1:	unset($data['installed']);		break;
				case 2: unset($data['notInstalled']);	break;
                case 9:
					$data = array('installed' => array('not_assign' => $this->notAssign));
					break;
				case 3:
					$data = array('installed' => array('core' => $this->core));
					break;
				case 4:
					$data = array('installed' => array('admin' => $this->admin));
					break;
				case 5:
					$data = array('installed' => array('normal' => $this->normal));
					break;
                case 7: 
                    $data = array('installed' => array('mobile' => $this->mobile));
                    break;
				case 6:
					unset($data['notInstalled']);
					unset($data['installed']['not_assign']);
                    break;
                case 8:
                    $data = array('installed' => array('removed' => $this->removed));
                    break;
			}
		}

		$this->layout->init(array(
			'style'  => "list",
			'method' => "POST"
		));
		$this->layout->addGroup('main', array('title' => 'Tên module'));
		$this->layout->addGroup('main2', array('title' => 'Loại module'));
		
		$this->layout->addItem('title', array('type' => 'text'), 'main');
		$this->layout->addItem('action_form', array('type' => 'hidden', 'value' => ''), 'main');
		$this->layout->addItem('module_type', array(
			'type' => 'select',
			'options' => FunctionLib::getOption($this->opt, $type)
		), 'main2');
		
		$dataForm = array(
			'html_search_label' => 'TÌM KIẾM',
			'html_view' => '&nbsp;'
		);
		$buttons = $this->layout->genButtonAuto(array(
            'title' => 'Xóa Cache',
            'style' => 0,
            'color' => 'pink',
			'icon'  => 'delete_forever',
            'type'  => 2,
            'size'  => 1,
            'ext' => array(
                'href' => Url::buildAdminURL('module',array('cmd'=>'delete_cache'))
            )
        ));
		$buttons .= '&nbsp;&nbsp;&nbsp;';
		$buttons .= $this->layout->genButtonAuto(array(
            'style' => 1,
			'icon'  => 'search',
            'color' => 'purple',
            'size'  => 1
	    ));
		$dataForm['html_search_button'] = $buttons;
		
		//not install theme
		if(!empty($data['notInstalled'])){
			$notIn = new Form('notIn');
			$notIn->perm = array('del' => true);
			$notIn->layout->init(array('style' =>	'grid'));
			$notIn->layout->addItemView('btn-del-check', array(
				'type'	=>	'del',
				'name' => 'install',
				'head' => array(
					'width' => 50
				),
				'ext' => array(
					'align' => 'center'
				)
			));
			$notIn->layout->addItemView('name', array('title' => 'Tên Module'));
			$notIn->layout->addItemView('last_changed', array('title' => 'Ngày tạo'));
			$notIn->layout->addItemView('remove', array(
				'title' => 'Xóa',
				'type' => 'icon',
				'icon' => "delete",
				'head' => array(
					'width' => 50
				),
				'ext' => array(
					'align' => 'center'
				)
			));
			$btIn = $notIn->layout->genButtonAuto(array(
				'type' => 1,
				'style'=> 1,
				'icon' => 'add_to_queue',
				'color'=> 'blue',
				'size' => 1,
				'ext'=> array(
					'onclick' => 'shop.module.install()',
					'title' => 'Cài đặt Module đã chọn'
				)
			));
			$gridView = $notIn->layout->genFormAuto($notIn, array(
				'items' => $data['notInstalled']
			), true);
			
			$dataForm['html_view'] .= $notIn->layout->genPanelAuto(array(
				'title' => 'CHƯA CÀI ĐẶT',
				'toggle' => true,
				'html' => $gridView.$btIn
			));
		}
		$gridView = '';
		if(!empty($data['installed'])){
			foreach($data['installed'] as $k => $v){
				if(!empty($v)){
					//fetch data
					foreach($v as $idx => $d){
						$d['init'] = $d['init'] == 0 ? '...' : '<i class="material-icons col-teal">check_circle</i>';
						if(isset($d['pages']) && !empty($d['pages'])){
							$pages = '';
							foreach($d['pages'] as $pageItem){
								$pages .= '<strong>[ <a href="'.$pageItem['link'].'" target="_blank">'.$pageItem['name'].'</a> ]</strong>&nbsp;&nbsp;&nbsp;';
							}
							$d['pages'] = $pages;
						}
						if($d['themes'] == '' && $d['themes_mobile'] == ''){
							$d['themes'] = '...';
						}else{
							$d['themes'] = $d['themes'] . $d['themes_mobile'];
						}
						$d['mobile'] = ($d['themes_mobile'] != '') ? '<i class="material-icons col-teal">check_circle</i>' : '...';
						if($d['onclick'] && $k!='removed'){
							$d['name'] = '<p style="cursor:pointer" onclick="shop.redirect(\''.$d['onclick'].'\')">'.$d['name'].' <a href="javascript:void(0)" class="pull-right"><i class="material-icons col-teal">web</i></a></p>';
						}
						$v[$idx] = $d;
					}
					$in = new Form('form'.$k);
					$in->perm = array('del' => true);
					$in->layout->init(array('style' =>	'grid'));
					if($k != 'core'){
						$in->layout->addItemView('btn-del-check', array(
							'type'	=>	'del',
							'name' => 'remove',
							'head' => array(
								'width' => 50
							),
							'ext' => array(
								'align' => 'center'
							)
						));
					}
					$in->layout->addItemView('id', array('title' => 'ID', 'head' => array('width' => 50), 'ext' => array('align' => 'center')));
					$in->layout->addItemView('name', array('title' => 'Tên Module'));
					if($k != 'core' && $k != 'admin'){
						$in->layout->addItemView('themes', array('title' => 'Themes'));
						if($k != 'normal'){
							$in->layout->addItemView('mobile', array('title' => 'Mobile', 'head' => array('width' => 80), 'ext' => array('align' => 'center')));
						}
					}
					if($k != 'not_assign'){
						$in->layout->addItemView('pages', array('title' => 'Các page đã gán'));
						$in->layout->addItemView('init', array('title' => 'Init', 'head' => array('width' => 50), 'ext' => array('align' => 'center')));
					}
					$hide = $k != 'not_assign' && $k != 'removed';
					if($stitle != '' || $type > 0){
						$hide = false;
					}
					
					$gridView .= $this->layout->genPanelAuto(array(
						'title' => $title[$k]. ' ('.count($v).')',
						'toggle' => true,
						'hide' => $hide,
						'color_head' => $k == 'removed' ? 'warning' : '',
						'html' => $in->layout->genFormAuto($in, array(
							'items' => $v
						), true)
					));
				}
			}
		}
		if($gridView != ''){
			$btIn = $this->layout->genButtonAuto(array(
				'type' => 1,
				'style'=> 1,
				'icon' => 'remove_from_queue',
				'color'=> 'red',
				'size' => 1,
				'ext'=> array(
					'onclick' => 'shop.module.uninstall()',
					'title' => 'Gỡ cài đặt Module đã chọn'
				)
			));
			$dataForm['html_view'] .= $this->layout->genPanelAuto(array(
				'title' => 'ĐÃ CÀI ĐẶT',
				'toggle' => true,
				'html' => $gridView.$btIn
			));
		}
		$this->layout->genFormAuto($this, $dataForm);
	}
	
	function on_submit(){
		$_SESSION['success_alert'] = '';
		$action = Url::getParam('action_form');
        if($action == 'install'){
			$modules = Url::getParam('install');
			if(!empty($modules)){
				$sql = array();
				$module_names = array();
				$module_installed = array();
				foreach($modules as $module_name){
					if(!in_array($module_name, $this->installed)){
						$moduleInfo = $this->allInfo[$module_name];
						$sql[] = "('{$moduleInfo['name']}','{$moduleInfo['themes']}','{$moduleInfo['themes_mobile']}')";
						$module_names[] = $moduleInfo['name'].($moduleInfo['themes'] != '' ? " (website: {$moduleInfo['themes']})" : "").($moduleInfo['themes_mobile'] != '' ? " (mobile: {$moduleInfo['themes_mobile']})" : "");

						//store to run code
						$module_installed[] = $moduleInfo;
					}
				}
				if(!empty($sql)){
					//insert DB
					$sql = "INSERT INTO ".T_MODULE." (`name`,`themes`,`themes_mobile`) VALUES ".implode(',',$sql);
					DB::query($sql);
					
					//import DB & run install code if have
					if(!empty($module_installed)){
						foreach($module_installed as $m){
							$sql_file = $m['dir'].'/install/db.sql';
							if(file_exists($sql_file)){
								$this->refreshSQLFile($sql_file);
								DB::import($sql_file, $msg);
								$this->refreshSQLFile($sql_file, true);
							}
							$install_file = $m['dir'].'/install/install.php';
							if(file_exists($install_file)){
								require_once $install_file;
								eval($m['name']."_install('".$m['name']."');");
							}
						}
					}

					//quet lai quyen & init
					$this->scanPermInit();

					//thong bao thanh cong
					$names = implode(', ',$module_names);
					$_SESSION['success_alert'] = "Module".(count($module_names) > 1 ? 's':'').": <b><em>$names</em></b> đã cài đặt thành công";

					Layout::update_all_page();

					//chuyển hướng về module
					Url::redirect("module");
				}
			}
		}else if($action == 'uninstall'){
			$module_ids = Url::getParam('remove');
			if(!empty($module_ids)){
				$module_names = array();
				$module_uninstalled = array();
				foreach($module_ids as $module_id){
					if($module_id > 0 && isset($this->nameByID[$module_id])){
						$module_names[] = $this->nameByID[$module_id];
						$module_uninstalled[] = $this->allInfo[$this->nameByID[$module_id]];
					}
				}
			
				$module_ids = implode(',', $module_ids);
				DB::delete(T_BLOCK, "module_id IN ($module_ids)"); 
				DB::delete(T_MODULE, "id IN ($module_ids)");

				//run uninstall code if have
				if(!empty($module_uninstalled)){
					foreach($module_uninstalled as $m){
						$install_file = $m['dir'].'/install/install.php';
						if(file_exists($install_file)){
							require_once $install_file;
							eval($m['name']."_uninstall('".$m['name']."');");
						}
					}
				}

				//quet lai quyen & init
				$this->scanPermInit();

				//thong bao thanh cong
				$names = implode(', ',$module_names);
				$_SESSION['success_alert'] = "Module".(count($module_names) > 1 ? 's':'').": <b><em>$names</em></b> đã được gỡ bỏ";

				Layout::update_all_page();

				//chuyển hướng về module
				Url::redirect("module");
			}
		}elseif($action != ''){
			$module_name = explode('del::',$action);
			if(isset($module_name[1]) && isset($this->allInfo[$module_name[1]])){
				$moduleInfo = $this->allInfo[$module_name[1]];
				FunctionLib::empty_all_dir($moduleInfo['dir'], true, true);
				$_SESSION['success_alert'] = "Module: <b><em>{$moduleInfo['name']}</em></b> đã được xóa hoàn toàn khỏi thư mục gốc";

				//chuyển hướng về module
				Url::redirect("module");
			}
		}
	}
	
	function init(){
		$this->opt = array(
			0 => '--- Chọn ---',
			1 => 'Chưa cài',
			2 => 'Đã cài',
            9 => 'Chưa gán',
            8 => 'Not Active',
            6 => 'Có init',
			3 => 'Core',
			4 => 'Admin',
			5 => 'Public',
            7 => 'Mobile',
		);
		$stitle = Url::getParam('title', '');
		$type = Url::getParamInt('module_type', 0);
		
		//danh sach toan bo thu muc modules
		$this->listModuleInDir(true, $this->allInfo);
		
		//load module tu theme dang active
		$this->listModuleInDir(true, $this->allInfo, CGlobal::$configs['themes']);
		
		//load module tu theme mobile dang active
		$this->listModuleInDir(true, $this->allInfo, '', CGlobal::$configs['themes_mobile']);
		
		//lay thong tin cac module
		$condition = array();
		if($stitle != ""){
			$condition[] = "name LIKE '%$stitle%'";
		}
		if($type == 6){
			$condition[] = "init = 1";
		}
		$condition = FunctionLib::addCondition($condition);
		if($condition != ''){
			$condition = " WHERE $condition";
		}
		
		$sql = "SELECT * FROM ".T_MODULE."$condition ORDER BY name";
		$re = DB::query($sql);
		if($re){
			while ($row = mysql_fetch_assoc($re)){
				//onclick
				$row['onclick'] = '';
				if(isset($_SESSION['assign_mode']) && !empty($_SESSION['assign_mode'])){
					$row['onclick'] = Url::buildAdminURL('edit_page', array('module_id' => $row['id'], 'id' => $_SESSION['assign_mode']['id'], 'region' => $_SESSION['assign_mode']['region'], 'mobile' => $_SESSION['assign_mode']['mobile']));
				}
				//get pages which contain module
				$row['pages'] = array();
				$re2 = DB::query('SELECT p.id,p.name FROM '.T_BLOCK.' b INNER JOIN '.T_PAGE.' p ON p.id=b.page_id WHERE module_id="'.$row['id'].'"');
				if($re2){
					while ($page = mysql_fetch_assoc($re2)){
						$page['link'] = Url::buildURL($page['name']);
						$row['pages'][$page['id']] = $page;
					}
				}
				if(isset($this->allInfo[$row['name']])){
					if(!empty($row['pages'])){
						//xem co phai module core khong
						if($this->isCoreModule($row['name'])){
							$this->core[$row['id']] = $row;
						}else{
							if(substr($row['name'], 0, 2) == 'am'){
								$this->admin[$row['id']] = $row;
							}else{
                                if($row['themes_mobile'] != ''){
                                    $this->mobile[$row['id']] = $row;
                                }else{
                                    $this->normal[$row['id']] = $row;
                                }
							}
						}
					}
					else{
						//neu chua dc gan se vao list chua dc gan
						$this->notAssign[$row['id']] = $row;
					}
				}
				if(!isset($this->allInfo[$row['name']])){
					$this->removed[] = $row;
				}else{
					$this->installed[] = $row['name'];
				}
				$this->idByName[$row['name']] = $row['id'];
				$this->nameByID[$row['id']] = $row['name'];
			}

			$installed = $this->installed;
			if($stitle != "" || $type > 0){
				$installed = array();
				$res = DB::query("SELECT name FROM ".T_MODULE);
				while($r = @mysql_fetch_assoc($res)){
					$installed[] = $r['name'];
				}
			}
			
			$this->notInstalled = array_diff($this->modules, $installed);
			if($stitle != ''){
				foreach($this->notInstalled as $k => $name){
					if(strpos($name, $stitle) === false){
						unset($this->notInstalled[$k]);
					}
				}
			}
		}
	}
	
	function scanPermInit($init = true){
		$res = DB::query("SELECT * FROM ".T_MODULE." ORDER BY name");
		$perm= array();
		$initIds = array();
		$configIds = array();

		$need_delete = array();
		while($r = @mysql_fetch_assoc($res)){
			if($r['name'] != '.svn'){
				$dir = DIR_MODULE;
				if($r['themes'] != ''){
					$dir = DIR_THEMES.'website/'.$r['themes'].'/modules/';
				}elseif($r['themes_mobile'] != ''){
					$dir = DIR_THEMES.'mobile/'.$r['themes_mobile'].'/modules/';
				}
				if(file_exists($dir.$r['name'].'/class.php')){
					require_once $dir.$r['name'].'/class.php';
					eval('$tmp = '.$r['name'].'::permission();
						if(!empty($tmp)) $perm["'.$r['name'].'"] = $tmp;');
					if($init && file_exists($dir.$r['name'].'/init.php')){
						$initIds[$r['id']] = $r;
					}
					if($init && file_exists($dir.$r['name'].'/config.php')){
						$configIds[$r['id']] = $r;
					}
				}else{
					$need_delete[$r['id']] = $r['id'];
				}
			}
		}
		//xoa module bi an
		if(!empty($need_delete)){
			$module_ids = implode(',', $need_delete);
			DB::delete(T_BLOCK, "module_id IN ($module_ids)"); 
			DB::delete(T_MODULE, "id IN ($module_ids)");
		}

		//set lai quyen module
		$perm = serialize($perm);
        ConfigSite::setConfigToDB('site_permission', $perm);

		//set lai init
		if($init){
			//innit
			$flag_init = 0;
			DB::query("UPDATE ".T_MODULE." SET init = 0");
			if(!empty($initIds)){
				$flag_init = 1;
				CacheLib::set('module_init', $initIds);
				DB::update(T_MODULE, array('init' => 1), "id IN (".implode(',', array_keys($initIds)).")");
			}
            ConfigSite::setConfigToDB('site_module_init', $flag_init);

			//config
			$flag_config = 0;
			if(!empty($configIds)){
				$flag_config = 1;
				CacheLib::set('module_config', $configIds);
				DB::update(T_MODULE, array('config' => 1), "id IN (".implode(',', array_keys($configIds)).")");
			}
            ConfigSite::setConfigToDB('site_module_config', $flag_config);
		}

		//xoa cache config
		ConfigSite::clearCacheConfig();

		//Xoa cache module
		ModuleInit::reset();
		
		//Xoa cache module
		ModuleConfig::reset();

		return true;
	}
	
	function getMoreInfo($module_list = array()){
		$temp = array();
		if(!empty($module_list)){
			foreach($module_list as $name){
				$temp[$name] = isset($this->allInfo[$name]) ? $this->allInfo[$name] : array();
				if(!isset($temp[$name]['id'])){
					$temp[$name]['id'] = $name;
					$temp[$name]['remove'] = "javascript:shop.module.delFile('$name')";
				}
			}
		}
		return $temp;
	}
	
	function listModuleInDir($get_more = false, &$more = array(), $themes = '', $themes_mobile = ''){
		$dir = DIR_MODULE;
		if($themes != '' || $themes_mobile != ''){
			if($themes != '' && $themes != 'sys'){
				$dir = DIR_THEMES.'website/'.$themes.'/modules/';
			}elseif($themes_mobile != '' && $themes_mobile != 'no_mobile'){
				$dir = DIR_THEMES.'mobile/'.$themes_mobile.'/modules/';
			}else{
				return array();
			}
		}
		$module_dirs = scandir($dir);
		unset($module_dirs[0]);
		unset($module_dirs[1]);
        if(isset($module_dirs[2]) && $module_dirs[2] == '.DS_Store'){
            unset($module_dirs[2]);
        }

		if($get_more){
			foreach($module_dirs as $k => $name){
				$dirModule = $dir.$name;
				$more[$name] = array(
					'dir' => $dirModule,
					'name' => $name,
					'themes' => $themes,
					'themes_mobile' => ($themes_mobile != '' && $themes_mobile != 'no_mobile') ? $themes_mobile : '',
					'last_access' => FunctionLib::dateFormat(fileatime ($dirModule), 'd/m/Y - H:i'),
					'last_modified' => FunctionLib::dateFormat(filemtime ($dirModule), 'd/m/Y - H:i'),
					'last_changed' => FunctionLib::dateFormat(filectime($dirModule), 'd/m/Y - H:i')
				);
				$this->modules[] = $name;
			}
		}
		return $module_dirs;
	}
	
	function isCoreModule($module_name = ''){
		return isset(CGlobal::$coreModules[$module_name]);
	}
	
	function cleanModuleList(){
		//cap nhat lai module da dc gan
		DB::query("UPDATE ".T_MODULE." SET assign = 1 ");
		if(!empty($this->notAssign)){
			DB::update(T_MODULE, array('assign' => 0), "id IN (".implode(',', array_keys($this->notAssign)).")");
		}
		
		//quet lai phan quyen & init voi cac module da dc gan
		$this->scanPermInit();
		
		//cap nhat lai cache trang sau khi xoa module
		Layout::update_all_page();
		
		//tra ve thong bao thanh cong
		$_SESSION['success_alert'] = "Module đã được xóa cache, nhận phân quyền, init";
		Url::redirect('module');
	}
	
	function refreshSQLFile($file = '', $restore = false){
		global $prefix;
		$content = file_get_contents($file);
		if($restore){
			$content = str_replace($prefix, '[[||PREFIX||]]', $content);
		}else{
			$content = str_replace('[[||PREFIX||]]', $prefix, $content);
		}
		$handle = @fopen($file, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}
}
