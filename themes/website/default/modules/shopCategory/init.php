<?php
//dinh nghia key
if(!defined('CATEGORY_KEY')){
	define('CATEGORY_KEY', 'category');
}

//dinh nghia bang
if(!defined('T_CATEGORY')){
    global $prefix;
    define('T_CATEGORY', $prefix . CATEGORY_KEY);
}

//dinh nghia duong dan thu muc anh
if (!defined('CATEGORY_FOLDER')) {
    ImageUrl::createFolderImg(CATEGORY_KEY, 'CATEGORY_FOLDER');
}

//set mac dinh CGlobal
$categoryType = array(
    1 => 'Tin tức',
    2 => 'Khác'
);
$category = array();
$cacheKey = 'allCategory-';
$time = 86400 * 365;
foreach ($categoryType as $k => $v) {
    $category[$k] = CacheLib::get($cacheKey . $k, $time);
    if (empty($category[$k])) {
        $category[$k] = Category::getCategoryArr($k);
        if (!empty($category[$k])) {
            CacheLib::set($cacheKey . $k, $category[$k], $time);
        }
    }
}

//set mac dinh de su dung trong code
CGlobal::set('category', $category, 'Mảng danh mục');
CGlobal::set('categoryType', $categoryType);