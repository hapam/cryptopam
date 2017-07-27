<?php
require_once '../core/Debug.php'; //System Debug...
require_once '../config/config.php';//System Config...
require_once '../core/Init.php';  //System Init...

$key = 'DKFHD^%$DJH123';
$clientID = 1;

$link = 'http://mail.ezcms.org/api.php';

$vars = array(
    'clientID' => $clientID,
    'publicKey'=> $key,
    'from' => 'noreply@goback.top',
    'fromName' => 'Coin Alert',
    'to' => 'lymanhha@gmail.com',
    'toName' => 'Ha Pam',
    'subject' => 'Test',
    'content' => '<p style="padding-bottom:10px">Dear,</p><p>BTC_ETC - 0.00720164 tăng lên <b>0.00763</b> ~ 5.95%</p><p style="padding-top:20px">Best regards,<br />Pam</p>'
);

$curl = new CURL();
$result = $curl->post($link, $vars);