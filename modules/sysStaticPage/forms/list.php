<?php
class listStaticPageForm extends Form {
	var $key = 'static_page_key', $perm, $link, $cmd = 'trang-tinh';

	function __construct(){
		parent::__construct();
		StaticP::getPageList();
		
		$this->perm = array(
            'add' => User::user_access("add page"),
            'edit' => User::user_access("edit page"),
            'del' => User::user_access("delete page")
        );
		$this->link = array(
            'add' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'add')),
            'edit' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'edit')),
            'del' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'delete'))
        );
	}
	
	function draw(){
		$pageListKey = isset(CGlobal::$configs[$this->key]) ? unserialize(CGlobal::$configs[$this->key]) : array();
		$pageList    = array();
		if(!empty($pageListKey)){
			foreach($pageListKey as $k => $v){
				$page = unserialize(CGlobal::$configs[$k]);
				$page['id'] = $page['url'];
				$page['t_url'] = WEB_ROOT.(($page['type'] == 'page') ? 'trang-' : '').$page['url'].'.html';
				$page['t_url'] = '<a href="'.$page['t_url'].'" target="_blank">'.$page['id'].'</a>';
                if(!isset($page['pid']) || (isset($page['pid']) && $page['pid'] == '')){
                    $page['trans'] = array();
                    $pageList[$page['url']] = $page;
                }else{
                    $pageList[$page['pid']]['trans'][$page['lang']] = $page;
                }
			}
			$langList = Language::$listLangOptions;
			$langDef = Language::$defaultLang;
			foreach($pageList as $id => $p){
				foreach($langList as $k => $i){
					if(isset($p['trans'][$k])){
						$pageList[$id][$k] = '<a href="'.$this->link['edit'].'?pid='.$p['url'].'&lang='.$k.'">Sửa</a>';
					}else{
						$pageList[$id][$k] = '<a href="'.$this->link['add'].'?pid='.$p['url'].'&lang='.$k.'" style="color:red">Dịch</a>';
					}
				}
			}
		}

		StaticP::autoList($this, array(
			'items' => $pageList
		));
    }
}