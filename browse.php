<?php
    require_once "core/Debug.php"; //System Debug...
    require_once "config/config.php";//System Config...
    require_once "core/Init.php";  //System Init...
    require_once "core/PageBeginAdmin.php";
    
    $browseForm = new Form('browse');
    $browseForm->link_js("js/jquery/jquery.form.js");
    $browseForm->link_js('modules/amGallery/js/browse.js');
    $browseForm->link_css('modules/amGallery/css/browse.css');
    
    
    $cat_id = Url::getParam('cat_id', 0);
    if($cat_id <= 0){
        $cat = array();
        $res = DB::query("SELECT * FROM ".T_GALLERY_CATS." ORDER BY created DESC, title DESC");
        while($r = @mysql_fetch_assoc($res)){
            $r['cover'] = 'css/images/no_preview.jpg';
            $url_vars = CGlobal::$urlVars;
            $url_vars['cat_id'] = $r['id'];
            $r['link'] = 'browse.php?'.http_build_query($url_vars);
            $cat[$r['id']] = $r;
        }
        
        $catIds = implode(',', array_keys($cat));
        $res = DB::query("SELECT * FROM ".T_GALLERY." WHERE is_cover = 1 AND cat_id IN ($catIds)");
        while($r = @mysql_fetch_assoc($res)){
            $cat[$r['cat_id']]['cover'] = Gallery::getImageGallery($r['image'], $r['created'], 350);
        }
    
echo '<h1 class="mTop20 mLeft20">Album Ảnh</h1><div class="mt28">';
echo '<div class="fl mLeft20" align="center" style="width:194px;height:240px;overflow:hidden;display:block;border:1px dashed #7f7f7f;background:url(css/images/add.png) no-repeat center 70px">
<a href="javascript:shop.browse.category.add()" style="display:block;width:100%;height:100%;color:#ccc;font-size:18px;line-height:290px">Tạo Album mới</a>
</div>';
        foreach ($cat as $c){
            echo'
        <div class="fl mLeft20" style="width:196px;height:242px;overflow:hidden;display:block">
            <div style="width:194px;height:194px;border:1px solid rgba(0, 0, 0, 0.1);background:url('.$c['cover'].') no-repeat center center">
                <a href="'.$c['link'].'" style="display:block;width:100%;height:100%"></a>
            </div>
            <div style="border: 1px solid #e9ebee;border-top: none;font-size: 11px;height: 28px;padding: 6px 8px 11px">
                <a href="'.$c['link'].'" style="display: block;max-width: 100%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;color:#365899"><b>'.$c['title'].'</b></a>
                <div style="color:#90949c;font-size:12px;line-height:25px">
                    <div class="fl">'.$c['total'].' ảnh <a href="javascript:shop.browse.category.refresh('.$c['id'].')"><img src="css/images/refresh.png" width="12" /></a></div>
                    <div class="fr">'.FunctionLib::dateFormat($c['created'],'d/m/Y').'</div>
                    <div class="c"></div>
                </div>
            </div>
        </div>';
        }
echo '
    <div class="c"></div>
</div>';
    }else{
        $instanceCKEditorName = Url::getParam('CKEditor','');
        $instanceCKEditorName = str_replace("'", '', $instanceCKEditorName);
        $CKEditorFuncNum = Url::getParamInt('CKEditorFuncNum', 0);

        $size = Url::getParam('size', 0);
        $size = $size > 0 ? $size : ImageUrl::getSize(GALLERY_KEY, 'max');

        $images = array();
        $cat = DB::fetch("SELECT * FROM ".T_GALLERY_CATS." WHERE id = ".$cat_id);
        $res = DB::query("SELECT * FROM ".T_GALLERY." WHERE cat_id = $cat_id ORDER BY sort ASC, title ASC, created DESC");
        while($r = @mysql_fetch_assoc($res)){
            $r['key'] = 'img'.$r['id'];
            $r['image'] = Gallery::getImageGallery($r['image'], $r['created'], 350);
            $r['image_out'] = Gallery::getImageGallery($r['image'], $r['created'], $size);
            $r['link'] = 'shop.browse.appendToCKEditor(\''.$r['key'].'\', \''.$instanceCKEditorName.'\', '.$CKEditorFuncNum.');';
            $images[$r['id']] = $r;
        }

echo '<h1 class="mTop20" align="center">'.$cat['title'].'</h1>
<div class="mTop5" align="center" style="color:#90949c;font-size:12px">Ngày tạo: '.FunctionLib::dateFormat($cat['created'],'d/m/Y').' · Số lượng: '.$cat['total'].' ảnh</div>
<div class="mTop20 mLeft20"><a href="javascript:window.history.go(-1)"><b>&laquo; Quay lại Album ảnh</b></a></div>
<div class="mt28">
    <input type="hidden" value="'.$cat_id.'" id="category-cur" />
    <div class="fl mLeft20" align="center" style="width:196px;height:220px;overflow:hidden;">
        <div style="border:1px dashed #7f7f7f;background:url(css/images/add.png) no-repeat center 60px;width:194px;height:194px;">
            <a href="javascript:shop.browse.image.upload()" style="display:block;width:100%;height:100%;color:#ccc;font-size:18px;line-height:270px">Upload ảnh</a>
        </div>
    </div>';
    foreach ($images as $i){
        echo'
    <div class="fl mLeft20" style="width:196px;height:220px;overflow:hidden;display:block">
        <div style="width:194px;height:194px;border:1px solid rgba(0, 0, 0, 0.1);background:url('.$i['image'].') no-repeat center center">
            <a href="javascript:void(0)" onclick="'.$i['link'].'" style="display:block;width:100%;height:100%">
                <input type="hidden" value="'.$i['image_out'].'" id="'.$i['key'].'" />
            </a>
        </div>
    </div>';
    }

echo '
    <div class="c"></div>
</div>';
    }

    require_once "core/PageEndAdmin.php";