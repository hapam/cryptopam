<?php

class ShopHome extends Module {

    static function permission() {}

    function __construct($row) {
        Module::Module($row);
        require_once 'forms/home.php';
        $this->add_form(new HomeForm());
    }

}
