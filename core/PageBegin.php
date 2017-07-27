<!DOCTYPE HTML>
<html>
<head>
    <title><?=CGlobal::$website_title?></title>
<!--  BEGIN META DEFINED  -->
    <?php
    if(CGlobal::$mobile[0]){
        echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />';
    }
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="EXPIRES" content="0" />
    <meta name="RESOURCE-TYPE" content="DOCUMENT" />
    <meta name="DISTRIBUTION" content="GLOBAL" />
    <meta name="AUTHOR" content="<?=WEB_AUTHOR?>" />
    <meta name="KEYWORDS" content="<?=CGlobal::$keywords?>" />
    <meta name="DESCRIPTION" content="<?php echo CGlobal::$meta_desc;?>" />
    <meta name="COPYRIGHT" content="Copyright (c) by <?=CGlobal::$site_name?>" />
<?php if( isset($_GET['page']) && in_array($_GET['page'], CGlobal::$pg_noIndex)){?>
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET" />
    <?php }else{?>
    <meta name="ROBOTS" content="<?=CGlobal::$robotContent?>" />
    <meta name="Googlebot" content="<?=CGlobal::$gBContent?>" />
    <?php } ?>
<!--<meta name="RATING" content="GENERAL" />-->
    <base href="<?=WEB_ROOT?>" />  
<!--  END META DEFINED  -->
    <?php echo '<link rel="shortcut icon" href="'.CGlobal::$favicon.'?v='.CGlobal::$version.'" />
';?>
    <script type="text/javascript" src="<?=WEB_ROOT?>js/core.js"></script>
    <?=Layout::$extraHeader;?>
    <?php FunctionLib::facebookMetaData();?>
    <?php FunctionLib::getGA();?>

</head>
<body <?php FunctionLib::getBodyBG(); ?>>
<?php RootPanel::init();?>