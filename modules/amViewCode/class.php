<?php

class amViewCode extends Module {

	function amViewCode($row) {
		Module::Module($row);
		$cmd = Url::getParamAdmin('cmd');
		if ($cmd == 'view-code' && User::is_root()) {
			$mode = Url::getParam("mode","");
			if ($mode == 'runSQL') {
				require_once 'forms/RunSql.php';
				$this->add_form(new RunSqlForm());
			}else{
				require_once 'forms/ViewCode.php';
				$this->add_form(new ViewCodeForm());
			}
		}
	}

}

