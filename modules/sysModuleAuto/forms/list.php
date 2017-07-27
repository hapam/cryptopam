<?php
class sysModuleAutoForm extends Form{
	function __construct(){
		parent::__construct();
		$this->link_js_me('admin_moduleAuto.js', __FILE__);
	}
	
	function draw(){
		$data = array(
			'title' => 'shopTest',
			'class_name' => 'Test',
			'admin' => 1,
			'theme' => CGlobal::$configs['themes'],
			'table' => 'ezcms__test',
			'java' => 1,
			'ajax' => 1,
			'lib'  => 1,
			'edit_mode' => 1,
			'search_form' => 1,
			'url' => 'test'
		);
		
		$this->layout->init(array('style' => 'edit'));
		$this->layout->addGroup('main', array('title' => 'Thông tin cơ bản'));
		$this->layout->addGroup('adminGroup', array(
			'title' => 'Quản trị',
			'ext' => array(
				'style' => 'display:'.($data['admin'] == 1 ? 'block' : 'none')
			)
		));
		$this->layout->addGroup('configGroup', array(
			'title' => 'Cấu hình Quản trị',
			'toggle'=> true,
			'ext' => array(
				'style' => 'display:'.($data['table'] != '' ? 'block' : 'none')
			)
		));
		//add form MAIN
		$this->layout->addItem('title', array(
			'type'	=> 'text',
			'title' => 'Tên module',
			'value' => Url::getParam('title', $data['title'])
		), 'main');
		$this->layout->addItem('class_name', array(
			'type'	=> 'text',
			'title' => 'Khóa định danh',
			'value' => Url::getParam('class_name', $data['class_name']),
			'caption' => 'Dùng để định nghĩa <b>Class</b>'
		), 'main');
		$this->layout->addItem('theme', array(
			'type'	=> 'select',
			'title' => 'Theme áp dụng',
			'options' => $this->getThemes($data['theme'])
		), 'main');
		$this->layout->addItem('admin', array(
			'type'	=> 'checkbox',
			'label' => 'Quản trị',
			'style' => 'onoff',
			'label_pos' => 'left',
			'checked' => Url::getParamInt('admin', $data['admin']) == 1,
			'ext' => array(
				'onchange' => "jQuery('#formGroup-adminGroup-parent').toggle()"
			)
		), 'main');
		$this->layout->addItem('java', array(
			'type'	=> 'checkbox',
			'label' => 'Javascripts',
			'style' => 'onoff',
			'label_pos' => 'left',
			'checked' => Url::getParamInt('java', $data['java']) == 1
		), 'main');
		$this->layout->addItem('ajax', array(
			'type'	=> 'checkbox',
			'label' => 'AJAX',
			'style' => 'onoff',
			'label_pos' => 'left',
			'checked' => Url::getParamInt('ajax', $data['ajax']) == 1,
			'line' => false
		), 'main');
		//$this->layout->addItem('lib', array(
		//	'type'	=> 'checkbox',
		//	'label' => 'Thư viện (class, sql, defined)',
		//	'style' => 'onoff',
		//	'label_pos' => 'left',
		//	'checked' => Url::getParamInt('lib', $data['lib']) == 1
		//), 'main');
		//add form ADMIN GROUP
		$this->layout->addItem('url', array(
			'type'	=> 'text',
			'title' => 'URL',
			'value' => Url::getParam('url', $data['url'])
		), 'adminGroup');
		$tables = array("" => "-- Chọn --") + $this->getTables();
		$this->layout->addItem('table', array(
			'type'	=> 'select',
			'title' => 'Dữ liệu',
			'options'=> FunctionLib::getOption($tables, $data['table']),
			'ext' => array(
				'onchange' => 'shop.moduleAuto.loadTable(this.value)'
			)
		), 'adminGroup');
		$this->layout->addItem('edit_mode_fine', array(
			'type'	=> 'checkbox',
			'label' => 'Thêm/Sửa',
			'style' => 'onoff',
			'label_pos' => 'left',
			'checked' => Url::getParamInt('edit_mode_fine', $data['edit_mode']) == 1,
			'ext' => array(
				'onchange' => 'shop.moduleAuto.loadTable()'
			)
		), 'adminGroup');
		$this->layout->addItem('search_form', array(
			'type'	=> 'checkbox',
			'label' => 'Tìm kiếm',
			'style' => 'onoff',
			'label_pos' => 'left',
			'checked' => Url::getParamInt('search_form', $data['search_form']) == 1,
			'ext' => array(
				'onchange' => 'shop.moduleAuto.loadTable()'
			),
			'line' => false
		), 'adminGroup');
		//add config group
		$this->layout->addItem('jsinit', array(
			'type' => 'html',
			'html' => $data['table'] != '' ? '<script type="text/javascript">shop.ready.add(function(){shop.moduleAuto.loadTable("'.$data['table'].'")}, true)</script>': ''
		), 'configGroup');
		
		
		$this->layout->genFormAuto($this);
	}
	
	function on_submit(){
		$module_name = Url::getParam('title');
		$class_name = Url::getParam('class_name');

		$theme = Url::getParam('theme');
		$theme = $this->refineTheme($theme);
		$ajax = Url::getParamInt('ajax', 0) == 1;
		$admin = Url::getParamInt('admin', 0) == 1;
		$js = Url::getParamInt('java', 0) == 1;

		$url = Url::getParam('url');
		$edit_mode = Url::getParam('edit_mode_fine') == 1;
		$table_name = Url::getParam('table');
		global $prefix;
		$module_key_name = str_replace($prefix, '', $table_name);
		$module_key = strtoupper($module_key_name);
		if($module_key[0] == '_'){
			$module_key = substr($module_key, 1, strlen($module_key) - 1);
		}
		//$constants = get_defined_constants();
		//$table_name = array_search($table_name, $constants, TRUE);

		//get cmd admin
		if(stripos($url, '.html') !== false){
			$url = substr($url,0, strlen($url)-5);
		}
		$cmd = explode('/',$url);
		$cmd = array_pop($cmd);

		if($module_name != ''){
			$dir = ROOT_PATH . 'themes/' . ($theme['is_mobile'] ? 'mobile' : 'website') . '/' . $theme['name'] . '/modules/' . $module_name;
			//tao thu muc
			FileHandler::CheckDir($dir);
			FileHandler::CheckDir($dir.'/forms');
			FileHandler::CheckDir($dir.'/tpl');
			//tao file ajax
			if($ajax){
				FileHandler::CheckDir($dir.'/ajax');
				$this->createAjaxFile($dir, $module_name);
			}
			//tao file js
			if($js){
				FileHandler::CheckDir($dir.'/js');
				$this->createJsFile($dir, $module_name, $admin, $ajax);
			}
			$cols = $this->showCol($groups, $checker);

			//tao install
			FileHandler::CheckDir($dir.'/install');
			$this->createInstallModule($dir, $module_name, $module_key, $module_key_name, $url, $checker['file']);
			
			//tao thu vien
			FileHandler::CheckDir($dir.'/conf');
			$this->createClassConf($dir, $class_name, $cmd, $edit_mode, $js, $theme, $module_key, $cols, $checker, $groups);

			//tao init file
			$this->createInitFile($dir, $module_key, $module_key_name, $checker);
			
			//tao file class
			$this->createClassFile($dir, $module_name, $admin, $cmd, $edit_mode);

			//tao file public
			$this->createFile($dir, $module_name, $js, $theme);

			if ($admin){
				//install module va gan vao page
				$this->installModuleAdmin($module_name, $theme, $url, $module_key);

				//tao file quan tri
				$this->createListFile($dir, $module_name, $cmd, $edit_mode, $js, $theme, $module_key, $cols, $checker, $class_name);
				if($edit_mode){
					$this->createEditFile($dir, $module_name, $cmd, $edit_mode, $js, $theme, $module_key, $cols, $checker, $class_name);
				}

				//reset cache
				require_once(ROOT_PATH.'/modules/sysModule/forms/list.php');
				$moduleAdmin = new ListModuleAdminForm();
				$moduleAdmin->scanPermInit();
				//$moduleAdmin->cleanModuleList();
				Layout::update_all_page();
			}
		}
		exit('Thành công');
	}
	
	function installModuleAdmin($name = '', $theme = array(), $url = '', $table_name = ''){
		$check = DB::fetch("SELECT * FROM ".T_MODULE." WHERE name = '$name'");
		if(empty($check)){
			$admin_page = DB::fetch("SELECT * FROM ".T_PAGE." WHERE name = 'admin'");
			$block_admin= DB::fetch("SELECT * FROM ".T_BLOCK." WHERE page_id = ".$admin_page['id']." AND region = 'main' ORDER BY position DESC LIMIT 0,1");
			
			//cai module vao co so du lieu
			$id = DB::insert(T_MODULE, array(
				'name' => $name,
				'themes' => $theme['is_mobile'] ? '' : $theme['name'],
				'themes_mobile' => $theme['is_mobile'] ? $theme['name'] : '',
				'assign' => 1,
				'init' => 1
			));
			
			//cam module vao page
			DB::insert(T_BLOCK, array(
				'module_id' =>	$id,
				'page_id'   =>	$admin_page['id'],
				'region'	=>	'main',
				'position'	=>	$block_admin['position'] + 1
			));
			
			//gan vao menu quan tri
			$pid = 1489487652;
			if(!Menu::getMenuItem($pid)){
				$pid = 0;
			}
			Menu::createMenu('Quản trị '.$name, 'admin/'.$url.'.html', 4, $pid, 'admin '.$name);
			
			//tao thu muc anh neu co
			ImageUrl::createFolderImg($url, $table_name.'_FOLDER');
			ImageUrl::addSizeImg($url, 150, 0);
			ConfigSite::writeConfigImage();
		}
	}
	
	function createInitFile($dir = '', $module_key = '', $module_key_name = '', $checker = array()){
		global $display;

		$display->add('mod_check', $checker);
		$display->add('mod_key', $module_key);
		$display->add('mod_key_name', $module_key_name);

		$content = $display->output('sample_init', true);

		$filename = $dir . '/init.php';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}
	function createInstallModule($dir = '', $name = '', $name_class_uper = '', $name_key = '', $url = '', $is_file = false){
		global $display;

		$display->add('mod_key', $name_key);
		$display->add('mod_name', $name);
		$display->add('mod_name_class', $name_class_uper);
		$display->add('mod_url', $url);
		$display->add('mod_file', $is_file);

		$content = $display->output('sample_install', true);
		$filename = $dir . '/install/install.php';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}

		$content = $display->output('sample_install_sql', true);
		$filename = $dir . '/install/db.sql';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}
	
	function createClassConf($dir = '', $name = '', $cmd = '', $edit_mode = false, $js = false, $theme = array(), $table_name = '', $cols = array(), $checker = array(), $groups = array()){
		global $display;

		$display->add('mod_check', $checker);
		$display->add('mod_name_class', $name);
		$display->add('mod_cmd', $cmd);
		$display->add('mod_js', $js);
		$display->add('mod_theme', $theme);
		$display->add('mod_edit', $edit_mode);
		$display->add('mod_cols', $cols);
		$display->add('mod_table', $table_name);
		$display->add('mod_groups', $groups);

		$content = $display->output('sample_class_conf', true);

		$filename = $dir . '/conf/' . $name . '.php';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}
	
	function createClassFile($dir = '', $name = '', $admin = false, $cmd = '', $edit_mode = false){
		global $display;
		$display->add('mod_cmd', $cmd);
		$display->add('mod_name', $name);
		$display->add('mod_edit', $edit_mode);
		$content = $display->output('sample_class'.($admin ? '_admin' : ''), true);

		$filename = $dir . '/class.php';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}
	
	function createFile($dir = '', $name = '', $js = false, $theme = array()){
		global $display;
		$display->add('mod_name', $name);
		$display->add('mod_js', $js);
		$display->add('mod_theme', $theme);
		$content = $display->output('sample_public', true);

		//tao file code
		$filename = $dir . '/forms/'.$name.'.php';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
		//tao file .tpl
		$filename = $dir . '/tpl/'.$name.'.tpl';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, '{$hello}');
			fclose($handle);
		}
	}
	
	function createListFile($dir = '', $name = '', $cmd = '', $edit_mode = false, $js = false, $theme = array(), $table_name = '', $cols = array(), $checker = array(), $name_class = ''){
		global $display;
		$display->add('mod_file', $checker['file']);
		$display->add('mod_name', $name);
		$display->add('mod_name_class', $name_class);
		$display->add('mod_cmd', $cmd);
		$display->add('mod_js', $js);
		$display->add('mod_theme', $theme);
		$display->add('mod_edit', $edit_mode);
		$display->add('mod_cols', $cols);
		$display->add('mod_table', $table_name);
		$content = $display->output('sample_admin_list', true);

		//tao file code
		$filename = $dir . '/forms/admin_list.php';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}
	
	function createEditFile($dir = '', $name = '', $cmd = '', $edit_mode = false, $js = false, $theme = array(), $table_name = '', $cols = array(), $checker = array(), $name_class = ''){
		global $display;
		$display->add('mod_file', $checker['file']);
		$display->add('mod_name_class', $name_class);
		$display->add('mod_check', $checker);
		$display->add('mod_name', $name);
		$display->add('mod_cmd', $cmd);
		$display->add('mod_js', $js);
		$display->add('mod_theme', $theme);
		$display->add('mod_edit', $edit_mode);
		$display->add('mod_cols', $cols);
		$display->add('mod_table', $table_name);
		$content = $display->output('sample_admin_edit', true);

		//tao file code
		$filename = $dir . '/forms/admin_edit.php';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}
	
	function createAjaxFile($dir = '', $name = ''){
		global $display;
		$display->add('mod_name', $name);
		$content = $display->output('sample_ajax', true);

		//tao file ajax
		$filename = $dir . '/ajax/ajax_'.$name.'.php';
		$handle = @fopen($filename, "w");
		if ($handle) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}
	
	function createJsFile($dir = '', $name = '', $admin = false, $ajax = false){
		$content = 'alert("hello");';
		if($ajax){
			$content = "shop.ajax_popup('act={$name}&code=test','POST',{number:10},
			function(j){
				if(j.err == 0){ // success
					alert(j.msg+' '+j.say);
				}else{
					alert(j.msg);
				}
		});";
		}
		//js public
		$handle = @fopen($dir . '/js/'.$name.'.js', "w");
		if ($handle) {
			fwrite($handle, 'shop.'.$name.' = {
	test: function(){
		'.$content.'
	}
};
shop.'.$name.'.test();');
			fclose($handle);
		}
		//js admin
		if($admin){
			$handle = @fopen($dir . '/js/'.$name.'Admin.js', "w");
			if ($handle) {
				fwrite($handle, 'shop.admin.'.$name.' = {
	test: function(){
		'.$content.'
	},
	onSubmit:function(frm){
		frm.submit();
	}
};
shop.admin.'.$name.'.test();');
				fclose($handle);
			}
		}
	}
	
	function refineTheme($theme = ''){
		return array(
			'is_mobile' => substr($theme,0,3) == 'mob',
			'name' => substr($theme,4, strlen($theme) - 4)
		);
	}
	
	function getThemes($def){
		require_once(ROOT_PATH.'/modules/sysThemes/forms/list.php');
		$theme = new ListThemesForm();
		//web
		$option1 = $theme->listThemesInDir(true);
		//mobile
		$option2 = $theme->listThemesInDir(true, true);
		unset($option2['no_mobile']);
		//mix
		$option = '<option value="">-- Chọn --</option>';
		$option.= '<optgroup label="Phiên bản PC">';
		foreach($option1 as $v){
			$k = 'web-'.$v;
			$option .= '<option value="'.$k.'"'.($v == $def ? ' selected': '').'>'.$v.'</option>';
		}
		$option.= '</optgroup>';
		$option.= '<optgroup label="Mobile">';
		foreach($option2 as $v){
			$k = 'mob-'.$v;
			$option .= '<option value="'.$k.'">'.$v.'</option>';
		}
		$option.= '</optgroup>';
		return $option;
	}
	
	function getTables(){
		$res = DB::query('SHOW TABLES');
		$tables = array();
		while($r = @mysql_fetch_assoc($res)){
			$r = array_pop($r);
			$tables[$r] = $r;
		}
		return $tables;
	}
	
	function showCol(&$groups = array(), &$checker = array()){
		$groups = array(
			'search' => array(),
			'opt' => array(),
			'time' => array()
		);
		$checker = array(
			'file'	=>	false,
			'text'	=>	false,
			'text_fck'	=>	false,
			'time'	=>	false,
			'pass'  => false
		);
		$table = Url::getParam('table');
		$tables = array();
		if($table != ''){
			$res = DB::query('SHOW COLUMNS FROM '.$table);
			while($r = @mysql_fetch_assoc($res)){
				$tmp = explode('(', $r['Type']);
				$r['t'] = $tmp[0];
				$r['l'] = isset($tmp[1]) ? substr($tmp[1], 0, -1) : 0;
				$opt = isset($_POST['option_'.$r['Field']]) ? $_POST['option_'.$r['Field']] : '';
				if($opt != ''){
					$new_line = array("\r\n", "\n", "\r");
					$opt = str_replace($new_line,'|',$opt);
					$opt = explode('|', $opt);
					if(!empty($opt)){
						$tmp = array();
						foreach($opt as $k => $v){
							$v = explode('=>', $v);
							if(!empty($v)){
								$tmp[$k] = array(
									'k' => trim($v[0]),
									'v' => trim($v[1]),
								);
							}
						}
						$opt = $tmp;
					}
				}else{
					$opt = array();
				}

				$tables[$r['Field']] = array(
					'name'  => $r['Field'],
					'type'  => $r['t'],
					'length'=> $r['l'],
					'filter'=> Url::getParamInt('filter_'.$r['Field']) == 1,
					'show'	=> Url::getParamInt('show_'.$r['Field']) == 1,
					'edit'	=> Url::getParamInt('edit_'.$r['Field']) == 1,
					'edit_t'=> Url::getParam('type_'.$r['Field']),
					'title'	=> Url::getParam('title_'.$r['Field']),
					'require'	=> Url::getParam('require_'.$r['Field']),
					'opt'	=> $opt
				);
				if($tables[$r['Field']]['filter']){
					if(in_array($tables[$r['Field']]['edit_t'], array('select', 'checkbox', 'checkbox-onoff', 'checkbox-group'))){
						$groups['opt'][$r['Field']] = $tables[$r['Field']];
					}elseif($tables[$r['Field']]['edit_t'] == 'time' || $r['Field'] == 'created'){
						$groups['time'][$r['Field']] = $tables[$r['Field']];
					}else{
						$groups['search'][$r['Field']] = $tables[$r['Field']];
					}
				}
				if($tables[$r['Field']]['edit_t'] == 'file'){
					$checker['file'] = true;
				}elseif($tables[$r['Field']]['edit_t'] == 'textarea-fck' || $tables[$r['Field']]['edit_t'] == 'textarea'){
					$checker['text'] = true;
					if($tables[$r['Field']]['edit_t'] == 'textarea-fck'){
						$checker['text_fck'] = true;
					}
				}elseif($tables[$r['Field']]['edit_t'] == 'time'){
					$checker['time'] = true;
				}elseif($tables[$r['Field']]['edit_t'] == 'password'){
					$checker['pass'] = true;
				}
			}
		}
		//System::debug($tables);
		return $tables;
	}
}
