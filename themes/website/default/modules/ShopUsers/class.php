<?php

class ShopUsers extends Module {

    static function permission() {
        return array();
    }

    function __construct($row) {
        Module::Module($row);

        require_once 'forms/users.php';
        $this->add_form(new ShopUserForm());
    }

}
