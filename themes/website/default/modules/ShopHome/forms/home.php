<?php

class HomeForm extends Form {

    function __construct() {
        $this->link_js_me("home.js", __FILE__);
    }

    function draw() {
        global $display;

        $display->add('tickers', Crypto::getTickerFromPolo());
        
        $display->add('user', User::$current->data);

        $display->add('time', FunctionLib::dateFormat(TIME_NOW, '', true));
        $display->add('timeLoad', ConfigSite::getConfigFromDB('time_load',5,false,'module_configs'));

        $display->output("home");
    }
}