<?php

class ShopUserForm extends Form {

    function __construct() {}

    function draw() {
        global $display;

        $display->add("user", User::$current->data);
        $display->add('tickers', Crypto::getTickerFromPolo());
        $display->add('default', Crypto::getPairsByUser());

        $this->beginForm();
        $display->output('users');
        $this->endForm();
    }

    function on_submit() {
        $uid = User::id();
        $done = array();
        $sql = "INSERT INTO ".T_COIN_USER." (`pair_id`, `uid`, `price`, `price_alert`, `alert`, `quantity`) VALUES ";

        $post = $_POST;
        foreach($post as $k => $v){
            if($k != '__myToken' && $k != 'form_block_id' && stripos($k, 'last') === false && $v > 0){
                preg_match("#[a-z]([0-9]+)#", $k, $pair_id);
                $pair_id = $pair_id[1];
                
                if(!isset($done[$pair_id])){
                    //get value
                    $buy = str_replace(',', '.', Url::getParam('buy'.$pair_id, 0));
                    $quan = str_replace(',', '.', Url::getParam('quan'.$pair_id, 0));
                    $alert = str_replace(',', '.', Url::getParam('alert'.$pair_id, 0));
                    $last = str_replace(',', '.', Url::getParam('last'.$pair_id, 0));
                    
                    $buy = $buy == '' ? 0 : $buy;
                    $quan = $quan == '' ? 0 : $quan;
                    $alert = $alert == '' ? 0 : $alert;

                    //del value
                    unset($post['buy'.$pair_id]);
                    unset($post['quan'.$pair_id]);
                    unset($post['alert'.$pair_id]);
                    unset($post['last'.$pair_id]);

                    if($alert > 0 && $buy == 0 && $quan == 0){
                        $buy = $last;
                    }
                    $price_alert = $buy + ($buy*$alert/100);
                    
                    $done[$pair_id] = "($pair_id, $uid, $buy, $price_alert, $alert, $quan)";
                }
            }
        }
        if(!empty($done)){
            $sql .= implode(',', $done);
            DB::delete(T_COIN_USER, "uid = $uid");
            DB::query($sql);
        }

        Url::redirect_current();
    }
}