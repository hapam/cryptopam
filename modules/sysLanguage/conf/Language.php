<?php
function t(){
    $args = func_get_args();
    return call_user_func_array(array('Language', 'trans'), $args);
}
class Language{
    static $cookie_key = 'super_lang';
    static $haveToTranslate = false;
    /**
     * Chuyen xau sang ngon ngu can dich
     * @example <b>PHP Code:</b>
     * <br />Language::trans('Hello')
     * <br />Language::trans('Hello %1, I am %2', 'VC', 'MC')
     * <br /><b>Smarty Code:</b>
     * <br />{'Hello'|t}
     * <br />{'Hello %1, I am %2'|t:'VC':'MC'}
     * @return string
     */
    static function trans(){
        $numberArgument = func_num_args();
        if ($numberArgument == 0) {
            return '';
        }

        $args = func_get_args();
        $str = $args[0];
        $localStr = '';
        if(StringLib::trimSpace($str) != '') {
            // Buoc 1: Tim xau dich tuong ung
            // Xin chao -> Hello; Xin chao %1, ban co nho %2 khong? -> Hi %1, do you remember %2?

            $lang = Language::$activeLang;
            $localStr = ($lang == self::$defaultLang) ? $str : self::checkWords($str,$lang,true);

            if ($str != '' && $localStr == '') {
                return '[ERROR TRANS: ' . $str . ']';
            } else {
                if ($numberArgument == 1) {
                    return $localStr;
                } else {
                    // Buoc 2: Ghep tham so vao cac gia tri %N
                    $search = array();
                    $replace = array();
                    $INVISIBLE_STRING = '#_' . chr(0) . '_#';
                    for ($i = 1; $i < $numberArgument; $i++) {
                        $search[] = '%' . $i;
                        $r = $args[$i];
                        $r = str_replace('%', $INVISIBLE_STRING, $r);//khong de bi replace gia tri cua tham so
                        $replace[] = $r;
                    }

                    $count = 0;
                    $localStr = str_replace($search, $replace, $localStr, $count);
                    if ($count > 0) {
                        $localStr = str_replace($INVISIBLE_STRING, '%', $localStr);
                    }
                }
            }
        }
        return $localStr;
    }

    static function checkWords($str = '', $lang = '', $insert = false, $type = 0){
        $str_check = strtolower(StringLib::stripUnicode($str));
        $str_check = StringLib::clean_value($str_check); // dam bao giong nhu khi luu db
        if (isset(self::$arrLang[$str_check]) && isset(self::$arrLang[$str_check][$lang])) {
            return htmlspecialchars_decode(self::$arrLang[$str_check][$lang]);
        }elseif($insert){
            //insert vao co so du lieu
            $id = self::addWordsToDB($str, self::$defaultLang, $type);
            if($id){
                self::$arrLang[$str_check][$lang] = '[ERROR TRANS: ' . $str . ']';
                return htmlspecialchars_decode(self::$arrLang[$str_check][$lang]);
            }
        }
        return "";
    }

    static function loadWordsFromLang($lang){
        //load danh sach tu khoa
        $arrIDs = array();
        $res = DB::query("SELECT * FROM ".T_LANG." WHERE is_main = 1 AND pid = 0 AND lang = '".self::$defaultLang."'");
        while($row = @mysql_fetch_assoc($res)){
            $key = strtolower(StringLib::stripUnicode($row['title']));
            self::$arrLang[$key] = array(self::$activeLang => '');
            $arrIDs[$row['id']] = $key;
        }

        //load ban dich theo lang
        if(!empty($arrIDs)) {
            $ids = implode(',', array_keys($arrIDs));
            $res = DB::query("SELECT * FROM " . T_LANG . " WHERE is_main = 0 AND pid IN ($ids) AND lang = '" . self::$activeLang . "'");
            while ($row = @mysql_fetch_assoc($res)) {
                $key = $arrIDs[$row['pid']];
                if (isset(self::$arrLang[$key])) {
                    self::$arrLang[$key][self::$activeLang] = $row['title'];
                }
            }
        }
    }

    static function addTransToDB($str, $lang, $pid){
        if($pid > 0 && $lang != self::$defaultLang){
            $str = StringLib::clean_value($str);
            $data = array(
                'title' => $str,
                'is_main' => 0,
                'lang' => $lang,
                'created' => TIME_NOW,
                'type' => 1,
                'pid' => $pid
            );
            return DB::insert(T_LANG, $data);
        }
        return false;
    }

    static function addWordsToDB($str, $lang, $type = 0){
        //type = 1 => from users created
        $str = StringLib::clean_value($str);
        $page_use = array(CGlobal::$current_page => 1);
        $data = array(
            'title' => $str,
            'is_main' => 1,
            'lang' => $lang,
            'created' => TIME_NOW,
            'page_used' => serialize($page_use),
            'type' => $type
        );
        return DB::insert(T_LANG, $data);
    }

    static function initLang($strLang){
        //fetch list lang
        if($strLang){
            self::$listLang = @unserialize($strLang);
        }
        if(!empty(self::$listLang)){
            foreach(self::$listLang as $k => $v){
                if($v == 1){
                    self::$activeLang = $k;
                }
                self::$listLangOptions[$k] = self::$languages[$k];
            }
        }else{
            self::$listLangOptions[self::$defaultLang] = self::$languages[self::$defaultLang];
            self::$listLang[self::$defaultLang] = 1;
        }

        //get active lang from cookie
        $langCookie = self::cookie();
        if($langCookie != '' && isset(self::$listLang[$langCookie])){
            self::$activeLang = $langCookie;
            foreach(self::$listLang as $k => $v) {
                self::$listLang[$k] = 0;
            }
            self::$listLang[$langCookie] = 1;
        }else{
            self::$activeLang = self::$defaultLang;
        }

        //load language
        if(self::$activeLang != self::$defaultLang){
            self::$haveToTranslate = true;
            self::loadWordsFromLang(self::$activeLang);
        }
    }

    static function cookie($set = false, $v = ''){
        if(!$set){
            return CookieLib::get_cookie(self::$cookie_key);
        }else{
            $v = empty($v) ? self::$defaultLang : $v;
            CookieLib::my_setcookie(self::$cookie_key, $v);
        }
    }

    static $defaultLang = 'vi';
    static $activeLang = '';
    static $listLangOptions = array();
    static $listLang = array();
    static $arrLang = array(
//        'trang chu' => array('en' => 'Home'),
//        '%1 nam truoc' => array('en' => '%1 years ago'),
//        'da co <b>%1</b> nguoi mua' => array('en' => '<b>%1</b> people purchased'),
//        '%1giam%2 %3%4%%5' => array('en' => '%1%4%%2 %3discount%5')
    );

    static $languages = array("ab" => "Abkhazian", "aa" => "Afar", "af" => "Afrikaans", "sq" => "Albanian", "am" => "Amharic", "ar" => "Arabic", "an" => "Aragonese", "hy" => "Armenian", "as" => "Assamese", "ay" => "Aymara", "az" => "Azerbaijani", "ba" => "Bashkir", "eu" => "Basque", "bn" => "Bengali (Bangla)", "dz" => "Bhutani", "bh" => "Bihari", "bi" => "Bislama", "br" => "Breton", "bg" => "Bulgarian", "my" => "Burmese", "be" => "Byelorussian (Belarusian)", "km" => "Cambodian", "ca" => "Catalan", "chr" => "Cherokee", "nya" => "Chewa", "zh" => "Chinese", "zh-Hans" => "Chinese (Simplified)", "zh-Hant" => "Chinese (Traditional)", "co" => "Corsican", "hr" => "Croatian", "cs" => "Czech", "da" => "Danish", "div" => "Divehi", "nl" => "Dutch", "bin" => "Edo", "en" => "English", "eo" => "Esperanto", "et" => "Estonian", "fo" => "Faeroese", "fa" => "Farsi", "fj" => "Fiji", "fi" => "Finnish", "fr" => "French", "fy" => "Frisian", "ful" => "Fulfulde", "gl" => "Galician", "gd" => "Gaelic (Scottish)", "gv" => "Gaelic (Manx)", "ka" => "Georgian", "de" => "German", "el" => "Greek", "kl" => "Greenlandic", "gn" => "Guarani", "gu" => "Gujarati", "ht" => "Haitian Creole", "ha" => "Hausa", "haw" => "Hawaiian", "iw" => "Hebrew he", "hi" => "Hindi", "hu" => "Hungarian", "is" => "Icelandic", "io" => "Ido", "ibo" => "Igbo", "in" => "Indonesian", "ia" => "Interlingua", "ie" => "Interlingue", "iu" => "Inuktitut", "ik" => "Inupiak", "ga" => "Irish", "it" => "Italian", "ja" => "Japanese", "jv" => "Javanese", "kn" => "Kannada", "kau" => "Kanuri", "ks" => "Kashmiri", "kk" => "Kazakh", "rw" => "Kinyarwanda (Ruanda)", "ky" => "Kirghiz", "rn" => "Kirundi (Rundi)", "kok" => "Konkani", "ko" => "Korean", "ku" => "Kurdish", "lo" => "Laothian", "la" => "Latin", "lv" => "Latvian (Lettish)", "li" => "Limburgish ( Limburger)", "ln" => "Lingala", "lt" => "Lithuanian", "mk" => "Macedonian", "mg" => "Malagasy", "ms" => "Malay", "ml" => "Malayalam", "mt" => "Maltese", "mi" => "Maori", "mr" => "Marathi", "mo" => "Moldavian", "mn" => "Mongolian", "na" => "Nauru", "ne" => "Nepali", "no" => "Norwegian", "oc" => "Occitan", "or" => "Oriya", "om" => "Oromo (Afaan Oromo)", "pap" => "Papiamentu", "ps" => "Pashto (Pushto)", "pl" => "Polish", "pt" => "Portuguese", "pa" => "Punjabi", "qu" => "Quechua", "rm" => "Rhaeto-Romance", "ro" => "Romanian", "ru" => "Russian", "sm" => "Samoan", "sg" => "Sangro", "sa" => "Sanskrit", "sr" => "Serbian", "sh" => "Serbo-Croatian", "st" => "Sesotho", "tn" => "Setswana", "sn" => "Shona", "ii" => "Sichuan Yi", "sd" => "Sindhi", "si" => "Sinhalese", "ss" => "Siswati", "sk" => "Slovak", "sl" => "Slovenian", "so" => "Somali", "es" => "Spanish", "su" => "Sundanese", "sw" => "Swahili (Kiswahili)", "sv" => "Swedish", "syr" => "Syriac", "tl" => "Tagalog", "tg" => "Tajik", "zgh" => "Tamazight", "ta" => "Tamil", "tt" => "Tatar", "te" => "Telugu", "th" => "Thai", "bo" => "Tibetan", "ti" => "Tigrinya", "to" => "Tonga", "ts" => "Tsonga", "tr" => "Turkish", "tk" => "Turkmen", "tw" => "Twi", "ug" => "Uighur", "uk" => "Ukrainian", "ur" => "Urdu", "uz" => "Uzbek", "ven" => "Venda", "vi" => "Tiếng Việt", "vo" => "Volapük", "wa" => "Wallon", "cy" => "Welsh", "wo" => "Wolof", "xh" => "Xhosa", "yi" => "Yiddish yi,", "yo" => "Yoruba", "zu" => "Zulu");
    
    static function autoList(&$form, $data = array()){
        
        
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET'
		));
        
		//add group search
		$form->layout->addGroup('main', array('title' => 'Thông tin'));
		$form->layout->addGroup('filter', array('title' => 'Bộ lọc'));
		
		//add item to search
		$form->layout->addItem('title', array(
			'type'	=> 'text',
			'title' => 'Từ khóa'
		), 'main');
		
		//filter
        $filterOptions = array('' => '-- Chọn --');
        foreach(Language::$listLangOptions as $k => $tit){
            if($k != Language::$defaultLang){
                $filterOptions[$k] = "Chưa có bản dịch ".$tit;
            }
        }
		$form->layout->addItem('lang', array(
			'type'	=> 'select',
			'title' => 'Lọc theo từ chưa dịch',
			'options' => FunctionLib::getOption($filterOptions, Url::getParam('lang', ''))
		), 'filter');
		
		//add view table
		$form->layout->addItemView('btn-del-check', array(
			'per'	=>	$form->perm['del'],
			'type'	=>	'del',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('id', array(
			'title' => 'ID',
			'order' => true,
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('title', array(
			'title' => 'Từ gốc ('.Language::$listLangOptions[Language::$defaultLang].')'
		));
		$form->layout->addItemView('page_used', array(
			'title' => 'Trang nguồn'
		));
        foreach(Language::$listLangOptions as $k => $tit){
            if($k != Language::$defaultLang){
                $form->layout->addItemView($k, array(
                    'title' => $k,
                    'head' => array(
                        'width' => 50
                    ),
                    'ext' => array(
                        'align' => 'center'
                    )
                ));
            }
        }
		$form->layout->addItemView('created', array(
			'title' => 'Thời gian',
            'head' => array(
				'width' => 100
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-edit', array(
			'title' =>	'Sửa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['edit'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		$form->layout->addItemView('btn-del', array(
			'title' =>	'Xóa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['del'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
        //header html
        $data['html_extra_head'] = self::newFormActiveLang($form);
		return $form->layout->genFormAuto($form, $data);
	}
    
    static function newFormActiveLang($form){
        $newform = new Form('langHeader');
        $newform->layout->init(array(
			'style'	=>	'list',
			'form'	=>	false
		));
        $buttons = $newform->layout->genButtonAuto(array(
            'title' => 'Thêm ngôn ngữ',
            'style' => 0,
            'color' => 'green',
            'icon'  => 'add',
            'type'  => 1,
            'size'  => 1,
            'per' => $form->perm['add_other_lang'],
            'ext' => array(
                'onclick' => 'shop.admin.lang.addLang()'
            )
        ));
        $buttons .= '&nbsp;&nbsp;&nbsp;'.$newform->layout->genButtonAuto(array(
            'title' => 'Kiểm tra trùng lặp',
            'style' => 0,
            'color' => 'purple',
            'icon'  => 'search',
            'type'  => 1,
            'size'  => 1,
            'ext' => array(
                'onclick' => "shop.redirect('".$form->link['check']."', true)"
            )
        ));
        $items = array();
        foreach(Language::$listLangOptions as $k => $tit){
            $tit = '<a href="javascript:shop.lang.change(\''.$k.'\')" title="Kích hoạt ngôn ngữ ở public site">'.$tit.'</a>';
            if($k == Language::$defaultLang){
                $tit .= ' <em style="color: #d3d3d3">(mặc định)</em>';
            }
            if($k == Language::$activeLang){
                $tit .= ' <em style="color: red">(Đang kích hoạt)</em>';
            }
            $items[$k] = array(
                'id' => $k,
                'title' => $tit,
                'remove' => "javascript:shop.admin.lang.removeLanguage('$k')"
            );
            if($k == Language::$defaultLang){
                unset($items[$k]['remove']);
            }
        }
        $newform->layout->addItemView('title', array(
			'title' => 'Ngôn ngữ đang được kích hoạt'
		));
        $newform->layout->addItemView('remove', array(
			'title' => 'Xóa',
            'type'  => 'icon',
            'icon'  => 'delete',
            'head' => array('width' => 50),
            'ext'  => array('align' => 'center')
		));
        return $newform->layout->genFormAuto($newform, array(
            'items' => $items,
            'html_view_buttons' => $buttons,
            'html_view_label' => $newform->layout->genLabelAuto(array('title' => 'KÍCH HOẠT NGÔN NGỮ', 'des' => 'Thêm ngôn ngữ, kích hoạt ngôn ngữ')),
            'html_search' => '&nbsp;'
        ), true);
    }
    
    static function autoEdit(&$form, &$data = array(), $action = ''){
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST'
		));
		
		//add group
		$form->layout->addGroup('main', array('title' => 'Thông tin cơ bản'));
		
		//add form main
		$form->layout->addItem('title', array(
            'type'	=> 'text',
            'title' => 'Từ gốc ('.Language::$listLangOptions[Language::$defaultLang].')',
            'required' => true,
            'value' => Url::getParam('title', $form->item['title']),
            'caption' => 'Có thể sử dụng chuỗi thay thế, ví dụ: <em><b>%1 đã gửi tin nhắn cho %2 lúc %3</b></em>'
        ), 'main');
        
		if($action == 'draw'){
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
}

