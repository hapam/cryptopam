<?php
class EmailTemplateViewer extends Module{
	static function permission(){
		return array(
			'view email tpl' => 'Xem Email mẫu'
		);
	}
	function __construct($row){
		Module::Module($row);
		if(Url::getParamAdmin('cmd') == 'email'){
            if(User::user_access('view email tpl',0,'access_denied')){
                require_once 'forms/email.php';
                $this->add_form(new EmailTemplateViewerForm());
            }
		}
	}
}

