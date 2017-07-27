<?php if(CGlobal::$current_page != 'export'){ ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?=CGlobal::$website_title?></title>
    <meta name="KEYWORDS" content="<?=CGlobal::$keywords?>" />
    <meta name="DESCRIPTION" content="<?php echo CGlobal::$meta_desc;?>" />

    <!-- Sweet alert Plugin -->
    <link href="<?=WEB_ROOT?>plugins/sweetalert/sweetalert.css" rel="stylesheet" />
    <script src="<?=WEB_ROOT?>plugins/sweetalert/sweetalert.min.js"></script>

    <!-- Favicon-->
    <?php echo '<link rel="icon" href="'.CGlobal::$favicon.'?v='.CGlobal::$version.'" type="image/x-icon" />';?>

    <!-- Google Fonts -->
    <link href="<?=WEB_ROOT?>css/fonts.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="<?=WEB_ROOT?>plugins/bootstrap/css/bootstrap.css?v=<?=CGlobal::$css_ver?>" rel="stylesheet">

    <!-- Bootstrap Select Css -->
    <link href="<?=WEB_ROOT?>plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

    <!-- Waves Effect Css -->
    <link href="<?=WEB_ROOT?>plugins/node-waves/waves.css?v=<?=CGlobal::$css_ver?>" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="<?=WEB_ROOT?>plugins/animate-css/animate.css?v=<?=CGlobal::$css_ver?>" rel="stylesheet" />

    <!-- core js by Pam -->
    <script type="text/javascript" src="<?=WEB_ROOT?>js/core.js"></script>
    
    <?=Layout::$extraHeader;?>
</head>
<body class="<?=FunctionLib::getBodyClass();?>">
<?php RootPanel::init();?>
    <!-- Page Loader -->
    <div class="page-loader-wrapper<?php if (DEBUG && User::is_root ()){ echo ' hidden';}?>">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
    <div class="search-bar">
        <div class="search-icon">
            <i class="material-icons">search</i>
        </div>
        <input type="text" placeholder="START TYPING...">
        <div class="close-search">
            <i class="material-icons">close</i>
        </div>
    </div>
    <!-- #END# Search Bar -->
<?php } ?>