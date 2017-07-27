<?php
class RootPanel{
	static $editmode = false, $page_id = 0, $page;
	
	static function init(){
		if(User::is_root()){
			self::$editmode = self::isEditMode();
			if(isset(CGlobal::$arrPage[CGlobal::$current_page])){
				self::$page_id = CGlobal::$arrPage[CGlobal::$current_page]['id'];
				self::$page = CGlobal::$arrPage[CGlobal::$current_page];
			}else{
				self::$page = array();
			}
		}
		//include js & css
		if(User::is_root()){
			$form = new Form('rootPanel');
			$form->link_css('modules/sysPanel/css/style_rootPanel.css');
			$form->link_js('modules/sysPanel/js/rootPanel.js');
			if(DEBUG){
				$form->link_js('js/ext/prettyprint.js');
			}
		}
	}
	
	static function drawPanel(){
		if(User::is_root()){
			$link = Url::buildURL('module');
			//fade
			echo '
<script type="text/javascript">
    //for public site
    shop.ready.add(function(){
        shop.rootPanel.go('.self::$page_id.', \''.$link.'\', '.json_encode(self::$page).');
    });
    //for admin
    shop.ready.add(function(){
        shop.rootPanel.go('.self::$page_id.', \''.$link.'\', '.json_encode(self::$page).');
    }, true);
</script>';
		}
	}
	
	static function isEditMode(){
		return CookieLib::get_cookie('editMode', 0) == 1;
	}
	
	static function drawModuleOnEditmode($end = false, $block_name = '', $block_id = 0){
		if(self::$editmode){
			if(!$end){
				$linkRemove = Url::buildAdminURL('edit_page', array('cmd' => 'delete_block', 'block_id' => $block_id, 'id' => self::$page_id));
				echo '<div class="block-root-panel" id="block-'.$block_id.'"><div class="block-name">'.$block_name.'
					<a href="javascript:void(0)" onclick="shop.rootPanel.editMode.removeModule('.$block_id.')" title="Bỏ module khỏi giao diện này">
						<img width="12" height="12" border="0" src="style/images/admin/icons/delete_button.gif">
					</a>
					<select onchange="shop.rootPanel.editMode.moving(this.value,'.$block_id.')">
						<option value="0">-Move-</option>
						<option value="top">Top</option>
						<option value="bottom">Bottom</option>
						<option value="up">Up</option>
						<option value="down">Down</option>
					</select>
				</div>';
			}else{
				echo '</div>';
			}
		}
	}
}
