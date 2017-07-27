    <?=Layout::$extraHeaderCSS;?>
    
    <!-- Custom Css -->
    <link href="<?=WEB_ROOT?>css/style.css?v=<?=CGlobal::$css_ver?>" rel="stylesheet">
    <link href="<?=WEB_ROOT?>css/default.css?v=<?=CGlobal::$css_ver?>" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="<?=WEB_ROOT?>css/themes/all-themes.css?v=<?=CGlobal::$css_ver?>" rel="stylesheet" />

    <script type="text/javascript">
        var query_string = "?<?=urlencode($_SERVER['QUERY_STRING']);?>",
            TIME_NOW = <?=TIME_NOW?>,
            BASE_TOKEN_NAME = "<?=TOKEN_KEY_NAME?>",
            BASE_TOKEN = "<?=CGlobal::$tokenData?>",
            COOKIE_ID = "<?=COOKIE_ID?>",
            BASE_URL = "<?=WEB_ROOT?>",
            IMG_URL = "<?=ImageUrl::getImageServerUrl()?>",
            SITE_NAME = "<?=CGlobal::$site_name?>",
            ADMIN_PROVINCE = "<?php if(User::is_root()) echo 0; else echo User::$current->data['province']; ?>",
            DOMAIN_NAME = "<?=DOMAIN?>",
            DOMAIN_COOKIE_STRING = "<?=DOMAIN_COOKIE_STRING?>",
<?php
        echo 'DOMAIN_COOKIE_REG_VALUE = document.URL.search(/'.DOMAIN_NAME_OK.'/i),';
        echo 'URL_PARAMS = '.json_encode(array('page' => CGlobal::$current_page) + CGlobal::$urlVars).',
        city_list = '.json_encode(CGlobal::$province).',
        WEB_STATUS = \''.CGlobal::$web_status.'\',
        WEB_STATUS_IMG = \''.CGlobal::$web_status_img.'\',
        WEB_STATUS_TXT = \''.CGlobal::$web_status_txt.'\',
        IS_ROOT = '.(int)User::is_root().',
        IS_ADMIN = '.(int)User::is_admin().',
        IS_LOGIN = '.(User::is_login()?User::id():0);
        $css = WEB_THEMES . 'website/' . CGlobal::$configs['themes'] . '/style/style_edit.css';
        echo ",CSS_EDITOR_LINK = '".$css."'";
        echo ';';
?>
    </script>
    
    <!-- Jquery Core Js -->
    <script src="<?=WEB_ROOT?>plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="<?=WEB_ROOT?>plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="<?=WEB_ROOT?>plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="<?=WEB_ROOT?>plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    
    <!-- Waves Effect Plugin Js -->
    <script src="<?=WEB_ROOT?>plugins/node-waves/waves.js"></script>

    <!-- Custom Js -->
    <script src="<?=WEB_ROOT?>js/admin/admin_function.js"></script>
    
    <?=Layout::$extraHeaderJS;?>
    <?=Layout::$extraFooter;?>

    <script src="<?=WEB_ROOT?>js/admin/admin.js"></script>

    <!--  START DEBUG   -->
    <?php RootPanel::drawPanel(); ?>
    <?php if (DEBUG && User::is_root ()){ ?>
    <div id="debug-container" style="display:none"><?php echo getDebug();?></div>
    <script type="text/javascript">shop.ready.add(shop.viewDebug, true)</script>
    <?php }?>
    <!-- END DEBUG -->
    <!-- AUTORUN -->
    <script type="text/javascript">jQuery(document).ready(shop.ready.run);</script>
    <!-- END AUTORUN -->
</body>
</html>