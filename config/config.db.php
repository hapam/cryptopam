<?php
// Master server DB
define('DB_MASTER_SERVER','localhost'); //db host
define('DB_MASTER_USER','root'); // user
define('DB_MASTER_PASSWORD',''); // password
define('DB_MASTER_NAME','crypto'); // db name
define('DB_CHARSET','UTF8'); //Set default charset DB : LATIN / UTF8
define('EMAIL_NOTIFY_DB', 'lymanhha@gmail.com'); // When has sql error, an email will be sent to this email

global $prefix;
$prefix = 'ezcms__';
