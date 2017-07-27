<?php
class amHelp extends Module {
	function amHelp($row) {
		Module::Module($row);
		$cmd = Url::getParamAdmin('cmd');
		if ($cmd == 'help') {
			$mode = Url::getParamAdmin("action");
			switch($mode){
				case 'list':
					require_once 'forms/List.php';
					$this->add_form(new ListForm());
					break;
				case 'edit':
					require_once 'forms/Edit.php';
					$this->add_form(new EditForm());
					break;
				case 'input-form':
					require_once 'forms/Edit.php';
					$this->add_form(new EditForm());
					break;
				default:
					require_once 'forms/Help.php';
					$this->add_form(new HelpForm());
			}
		}
	}
}