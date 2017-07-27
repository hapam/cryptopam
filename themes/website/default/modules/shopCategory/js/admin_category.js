shop.admin.category = {
    onSubmit:function(frm){
        if(frm.title.value == ''){
            shop.closeErr('#type');
            shop.raiseError('#title', "Chưa nhập tên danh mục", true);
        }else if(frm.type.value == -1){
            shop.closeErr('#title');
            shop.raiseError('#type', "Bắt buộc phải chọn", true);
        }else{
            frm.submit();
        }
    },
    onSubmit_location:function(frm){
        if(frm.title.value == ''){
            shop.raiseError('#title', "Chưa nhập tên danh mục", true);
        }else{
            frm.submit();
        }
    },
    toggle:function(obj){
        var id = obj.id;
        if(jQuery(obj).hasClass('tog')){
            jQuery('.close'+id).removeClass('hidden');
            jQuery(obj).removeClass('tog');
            jQuery('.tog').each(function(){
                jQuery('.close'+this.id).addClass('hidden');
            });
        }else{
            jQuery('.close'+id).addClass('hidden');
            jQuery(obj).addClass('tog');
        }
    },
    loadParentCat:function(type){
        if(type != -1){
            if(shop.is_exists(shop._store.variable['cat'+type])){
                jQuery('#parent_id').html(shop._store.variable['cat'+type]);
            }else{
                shop.ajax_popup('act=category&code=load-cat','POST',{type:type},
                    function(j){
                        if(j.err == 0){
                            shop._store.variable['cat'+type] = j.data;
                            jQuery('#parent_id').html(j.data);
                        }else{
                            alert(j.msg);
                        }
                    });
            }
        }
    }
};