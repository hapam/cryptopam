<?php
//them prefix vao cac bang cho du an moi, co the dung phpmyadmin change prefix hang loat
global $prefix;

define('T_BLOCK', $prefix.'_block');
define('T_CONFIGS', $prefix."_configs");
define('T_MODULE', $prefix."_module");
define('T_PAGE', $prefix.'_page');
define('T_ROLES',$prefix.'_roles');
define('T_USER_ROLES', $prefix.'_users_roles');
define('T_USERS',$prefix.'_users');
define('T_USER_OTP',$prefix.'_users_otp');
define('T_USER_BLOCK', $prefix.'_user_block');
define('T_LOGS', $prefix.'_logs');
define('T_LANG', $prefix.'_lang');
define('T_PROVINCE', $prefix.'_province');
define('T_FILE_UPLOAD', $prefix.'_file_upload');