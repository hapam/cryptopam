shop.admin.lang = {
    countries:null,
    addLang:function(){
        var html, options = '<option value="NONE">-- Chọn --</option>';
        for(var i in shop.admin.lang.countries){
            if(i != 'vi'){
                options += '<option value="'+i+'">'+ shop.admin.lang.countries[i] +'</option>';
            }
        }
        html = shop.join
        ('<div id="popup-form">')
            ('<table id="pass-changed" border="0" cellpadding="8" cellspacing="0" align="center" width="100%">')
                ('<tr>')
                    ('<td width="80">Ngôn ngữ</td>')
                    ('<td><select id="langOpts">'+options+'</select></td>')
                ('</tr>')
            ('</table>')
        ('</div>')
        ('<div class="popup-footer" align="right">')
            ('<button type="button" onclick="shop.admin.lang.addLangSub()">Thêm mới</button>')
            ('<button type="button" onclick="shop.hide_overlay_popup(\'lang-add\');">Hủy bỏ</button>')
        ('</div>')();
        shop.show_overlay_popup('lang-add', 'Thêm ngôn ngữ mới', html,
        {
            content: {
                'width' : '400px'
            },
            release:function(){
                if(shop.is_exists(jQuery.uniform)){
                    jQuery('#langOpts').uniform();
                }
            }
        });
    },
    addLangSub:function(){
        var langVal = jQuery('#langOpts').val();
        if(langVal != 'NONE'){
            shop.ajax_popup('act=lang&code=add-lang','POST',{id:langVal},
                function(j){
                    if(j.err == 0){
                        alert('Thêm ngôn ngữ thành công', shop.reload);
                    }else{
                        alert(j.msg);
                    }
                });
        }else{
            alert('Vui lòng chọn ngông ngữ')
        }
    },
    removeLanguage:function(lang){
        shop.confirm('Bạn có chắc chắn muốn ngừng kích hoạt ngôn ngữ '+shop.admin.lang.countries[lang]+' ?', function(){
            shop.ajax_popup('act=lang&code=remove-lang','POST',{id:lang},
                function(j){
                    if(j.err == 0){
                        alert('Đã ngừng kích hoạt ngôn ngữ '+shop.admin.lang.countries[lang], shop.reload);
                    }else{
                        alert(j.msg);
                    }
                });
        });
    },
    addTrans: function (lang, pid, edit_id) {
        var html, title = jQuery('#word'+pid).html(), def = '';
        if(edit_id > 0){
            def = jQuery('#trans_text_'+lang+'_'+pid).html();
        }
        html = shop.join
        ('<div id="popup-form">')
            ('<table id="pass-changed" border="0" cellpadding="8" cellspacing="0" align="center" width="100%">')
                ('<tr>')
                    ('<td width="80">Từ gốc</td>')
                    ('<td style="font-size: 11px;font-style: italic;color: dodgerblue">'+title+'</td>')
                ('</tr>')
                ('<tr>')
                    ('<td width="80">Dịch</td>')
                    ('<td><input type="text" id="trans_word" size="75" value="'+def+'" /></td>')
                ('</tr>')
            ('</table>')
        ('</div>')
        ('<div class="popup-footer" align="right">')
            ('<button type="button" onclick="shop.admin.lang.addTransSub(\''+lang+'\','+pid+','+edit_id+')">Hoàn thành</button>')
            ('<button type="button" onclick="shop.hide_overlay_popup(\'trans-add\');">Hủy bỏ</button>')
        ('</div>')();
        shop.show_overlay_popup('trans-add', 'Dịch sang '+shop.admin.lang.countries[lang], html,
            {
                content: {
                    'width' : '600px'
                },
                release:function(){
                    if(shop.is_exists(jQuery.uniform)){
                        jQuery('#trans_word').uniform();
                    }
                }
            });
    },
    addTransSub:function(lang, pid, edit_id){
        var langVal = jQuery('#trans_word').val();
        if(langVal != ''){
            shop.ajax_popup('act=lang&code=add-trans','POST',{pid:pid, lang:lang, txt:langVal, edit_id:edit_id},
                function(j){
                    if(j.err == 0){
                        shop.hide_overlay_popup('trans-add');
                        if(!(edit_id > 0)){
                            jQuery('#ctrlLang_'+lang+'_'+pid).css({'color':'darkblue'}).html('Sửa').click(function(){
                                shop.admin.lang.addTrans(lang, pid, j.id);
                            });
                        }
                        var p = jQuery('#ctrlLang_'+lang+'_'+pid).parent();
                        jQuery('#trans_text_'+lang+'_'+pid).remove();
                        jQuery(p).append('<div id="trans_text_'+lang+'_'+pid+'" style="display: none">'+langVal+'</div>');
                        alert('Đã thêm bản dịch '+shop.admin.lang.countries[lang]);
                    }else{
                        alert(j.msg);
                    }
                });
        }else{
            alert('Vui lòng nhập bản dịch');
            jQuery('#word'+pid).focus();
        }
    }
};