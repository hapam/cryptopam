<?php

class ShopFollow extends Module {

    static function permission() {}

    function __construct($row) {
        Module::Module($row);
        require_once 'forms/follow.php';
        $this->add_form(new FollowForm());
    }

}
