<?php
    //Quy tac dat ten file: ajax_[action].php

    require_once 'core/Debug.php'; //System Debug...
    require_once 'config/config.php';//System Config...
    require_once 'core/Init.php';  //System Init...
    
    $code = Url::getParam('code');
    $page = Url::getParam('act', 'index');

    //kiem tra token
    if($code == 'multi-upload' || ($page == 'index' && $code == 'upload')){
        //day la TH multi upload, do goi gia trinh duyet nen session khac voi session da khai bao ban dau
    }elseif(CGlobal::$tokenData !== Url::getParam(TOKEN_KEY_NAME)) {
        exit('invalid token '.Url::getParam(TOKEN_KEY_NAME).' | CGlobal: '.CGlobal::$tokenData);
    }

    $class = 'ajax_'.$page;
    $run_me = new $class ();
    $run_me->playme();

    System::halt();
