shop.admin.menu = {
	onSubmit:function(frm){
		if(shop.util_trim(frm.title.value) == ''){
			shop.raiseError('#title', 'Chưa nhập tiêu đề', true);
			return;
		}else{
            if(shop.get_ele('type').value == 1){
                if(!shop.is_link(frm.link.value)){
                    shop.raiseError('#link', 'Link không hợp lệ', true);
                    return;
                }
            }else{
                if(shop.is_link(frm.link.value)){
                    shop.raiseError('#link', 'Link không hợp lệ', true);
                    return;
                }
            }
        }
		frm.submit();
	}
};