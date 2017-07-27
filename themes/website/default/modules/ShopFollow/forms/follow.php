<?php

class FollowForm extends Form {

    function __construct() {
        $this->link_js_me("follow.js", __FILE__);
    }

    function draw() {
        global $display;

        $def = Crypto::getPairsByUser();

        $pairs = array();
        if(!empty($def)){
            $res = DB::query("SELECT id, pair FROM ". T_COIN_PAIR ." WHERE id IN (".implode(',', array_keys($def)).")");
            while($r = @mysql_fetch_assoc($res)){
                $def[$r['id']]['pair_name'] = $r['pair'];
            }
        }

        $display->add('default', $def);
        $display->add('defJson', json_encode($def));
        
        $display->add('tickers', Crypto::getTickerFromPolo());
        
        $display->add('user', User::$current->data);

        $display->add('time', FunctionLib::dateFormat(TIME_NOW, '', true));
        $display->add('timeLoad', ConfigSite::getConfigFromDB('time_load',5,false,'module_configs'));

        $display->output("follow");
    }
}