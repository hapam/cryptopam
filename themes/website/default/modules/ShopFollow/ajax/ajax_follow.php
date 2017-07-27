<?php

if (preg_match("/" . basename(__FILE__) . "/", $_SERVER ['PHP_SELF'])) {
    die("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_follow {

    function playme() {
        $code = Url::getParam('code');

        switch ($code) {
            case 'load':
                $this->loadFromPolo();
                break;
            default: $this->home();
        }
    }
    
    function loadFromPolo(){
        FunctionLib::JsonSuccess("ok", array("data" => Crypto::getTickerFromPolo()), true);
    }
    
    function home() {
        die("Nothing to do...");
    }
}