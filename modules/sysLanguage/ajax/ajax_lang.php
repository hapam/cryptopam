<?php
if (preg_match ( "/".basename ( __FILE__ )."/", $_SERVER ['PHP_SELF'] )) {
	die ("<h1>Incorrect access</h1>You cannot access this file directly.");
}

class ajax_lang {
	function playme(){
		$code = Url::getParam('code');

		switch( $code ){
            case 'add-lang-auto':
                $this->addLangAuto();
                break;
			case 'add-lang':
				$this->addLang();
				break;
            case 'remove-lang':
                $this->removeLang();
                break;
            case 'change-lang':
                $this->changeLang();
                break;
            case 'add-trans':
				$this->addTrans();
				break;
			default: $this->home();
		}
	}
	
	function addLang(){
        if(User::user_access('add lang')) {
            $lang = Url::getParam('id', '');
            if ($lang != '') {
                $allLang = Language::$listLang;
                if(!isset($allLang[$lang])){
                    $allLang[$lang] = 0;
                    ConfigSite::setConfigToDB('lang', serialize($allLang));
                    //xoa cache config
					ConfigSite::clearCacheConfig();
                    FunctionLib::JsonSuccess('ok', false, true);
                }
                FunctionLib::JsonErr('Ngôn ngữ này đã tồn tại', false, true);
            }
            FunctionLib::JsonErr('Dữ liệu không tồn tại', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
	}

    function removeLang(){
        if(User::user_access('delete lang')) {
            $lang = Url::getParam('id', '');
            if ($lang != '') {
                $allLang = Language::$listLang;
                if(isset($allLang[$lang])){
                    unset($allLang[$lang]);
                    ConfigSite::setConfigToDB('lang', serialize($allLang));
                    //xoa cache config
					ConfigSite::clearCacheConfig();
                    FunctionLib::JsonSuccess('ok', false, true);
                }
                FunctionLib::JsonErr('Ngôn ngữ này hiện tại đã ngừng kích hoạt', false, true);
            }
            FunctionLib::JsonErr('Dữ liệu không tồn tại', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
    }

    function changeLang(){
        $lang = Url::getParam('id', '');
        if ($lang != '') {
            $allLang = Language::$listLang;
            if(isset($allLang[$lang])){
                Language::cookie(true, $lang);
                FunctionLib::JsonSuccess('ok', false, true);
            }
            FunctionLib::JsonErr('Ngôn ngữ này hiện tại đã ngừng kích hoạt', false, true);
        }
        FunctionLib::JsonErr('Dữ liệu không tồn tại', false, true);
    }

    function addTrans(){
        if(User::user_access('add lang')) {
            $pid = Url::getParamInt('pid', 0);
            $lang = Url::getParam('lang', '');
            $txt = Url::getParam('txt', '');
            if ($lang != '' && $pid > 0) {
                $allLang = Language::$listLang;
                if(isset($allLang[$lang])){
                    $id = Language::addTransToDB($txt, $lang, $pid);
                    if($id){
                        FunctionLib::JsonSuccess('ok',array('id'=>$id), true);
                    }
                    FunctionLib::JsonErr('Lỗi dữ liệu', false, true);
                }
                FunctionLib::JsonErr('Ngôn ngữ này đã ngừng kích hoạt', false, true);
            }
            FunctionLib::JsonErr('Dữ liệu không hợp lệ', false, true);
        }
        FunctionLib::JsonErr('Không có quyền', false, true);
    }

    function addLangAuto(){
        $txt = Url::getParam('txt', '');
        if (Language::$defaultLang != Language::$activeLang) {
            if(Language::addWordsToDB($txt, Language::$defaultLang)){
                FunctionLib::JsonSuccess('ok',false, true);
            }
            FunctionLib::JsonErr('Lỗi dữ liệu', false, true);
        }
        FunctionLib::JsonErr('Dữ liệu không hợp lệ', false, true);
    }

	function home(){die("Nothing to do...");}
	
}//class
