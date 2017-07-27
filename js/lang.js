shop.lang = {
    trans:{},
    active:'vi',
    default:'vi',
    init:function(def, active, arr){
        shop.lang.trans = arr ? arr : [];
        shop.lang.def = def ? def : 'vi';
        shop.lang.active = active ? active : 'vi';
    },
    t:function(arrInput){
        var na = arrInput.length;
        if (na == 0) return '';
        var txt = arrInput[0];
        var localTxt = '';
        if(txt != ''){
            localTxt = (shop.lang.default == shop.lang.active) ? txt : shop.lang.checkWords(txt, shop.lang.active, true);
        }
        if (localTxt == '') {
            return '[ERROR TRANS: '+txt+']';
        } else {
            var i = 0;
            var r = '', INVISIBLE_STR = '#~@_@~#';
            for (i = 1; i < na; i++) {
                r = new String(arrInput[i]);
                r = r.replace(/%/g, INVISIBLE_STR);
                localTxt = localTxt.replace('%'.concat(i), r);
            }
            localTxt = localTxt.replace(/#~@_@~#/g, '%');
            return localTxt;
        }
    },
    checkWords:function(txt, lang, insert){
        var str_insert = txt;
        txt = shop.string.stripUnicode(txt);
        txt = txt.toLowerCase(txt);

        if(shop.lang.trans[txt]){
            return shop.lang.trans[txt][lang];
        }else if(insert){
            //goi request add to DB
            shop.lang.addWords(str_insert);
        }
        return '';
    },
    addWords:function(txt, cb){
        shop.ajax_popup('act=lang&code=add-lang-auto','POST',{txt:txt},
            function(j){
                if(cb){
                    cb(j);
                }
            });
    },
    change:function(lang){
        shop.ajax_popup('act=lang&code=change-lang','POST',{id:lang},
            function(j){
                if(j.err == 0){
                    shop.reload();
                }else{
                    alert(j.msg);
                }
            });
    }
};

function t(){var arrIn = arguments; return shop.lang.t(arrIn)};