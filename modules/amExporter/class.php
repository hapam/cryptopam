<?php
class amExporter extends Module{
    static function permission(){
        return array(
			'exporter' => 'Xuất các loại văn bản'
		);
    }
    function __construct($row){
        Module::Module($row);
		if(User::user_access('exporter',0,'access_denied')){
			$cmd = Url::getParamAdmin('cmd','');
			switch($cmd){
				case 'order':
					require_once 'forms/booking.php';
					$this->add_form(new bookingExportForm());
				break;
			}
		}
    }
}