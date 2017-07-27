    <?php echo '<link href="'.FunctionLib::getPathThemes().'style/style.css?v='.CGlobal::$css_ver.'" rel="stylesheet" type="text/css" />';?>

    <?=Layout::$extraHeaderCSS;?>

    <?php echo '<link href="'.FunctionLib::getPathThemes().'style/default.css?v='.CGlobal::$css_ver.'" rel="stylesheet" type="text/css" />';?>
    
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
        echo ';';
?>
    </script>
    <script src="<?=WEB_ROOT?>plugins/jquery/jquery.min.js"></script>
    <!--    for lang -->
    <script type="text/javascript" src="<?=WEB_ROOT?>js/ext/string.js"></script>
    <script type="text/javascript" src="<?=WEB_ROOT?>js/lang.js"></script>

    <?=Layout::$extraHeaderJS;?>
    <?=Layout::$extraFooter;?>

    <!--  START DEBUG   -->
    <?php RootPanel::drawPanel(); ?>
    <?php if (DEBUG && User::is_root ()){ ?>
    <div id="debug-container" style="display:none"><?php echo getDebug();?></div>
    <script type="text/javascript">shop.ready.add(shop.viewDebug)</script>
    <?php }?>

    <!-- AUTORUN -->
    <script type="text/javascript">
        shop.lang.init('<?=Language::$defaultLang?>', '<?=Language::$activeLang?>', <?=json_encode(Language::$arrLang)?>);
        jQuery(document).ready(shop.ready.run);
    </script>
</body>
</html>