shop.admin.province = {
	onSubmit:function(frm){
		if(frm.title.value == ''){
			shop.show_popup_message("Chưa nhập tên vùng miền", "Thông báo lỗi", -1);
			frm.title.focus();
		}
		else{
			frm.submit();
		}
	},
    addMessengerInput: function(alterId,type){
        var num = (parseInt(shop.get_ele(alterId+'_num').value) + 1);
        var name = type+num;
        var nameDisplay = type+'Name'+num;
		var html = shop.join
		('<div class="row clearfix">')
			('<div class="col-md-6">')
				('<div class="input-group">')
					('<span class="input-group-addon">')
						('<i class="material-icons">person</i>')
					('</span>')
					('<div class="form-line">')
						('<input type="text" class="form-control" placeholder="'+type+' ID" name="'+name+'" value="" />')
					('</div>')
				('</div>')
			('</div>')
			('<div class="col-md-6">')
				('<div class="input-group">')
					('<span class="input-group-addon">')
						('<i class="material-icons">face</i>')
					('</span>')
					('<div class="form-line">')
						('<input type="text" class="form-control" placeholder="Tên hiển thị" name="'+nameDisplay+'" value="" />')
					('</div>')
				('</div>')
			('</div>')
		('</div>')
		();
        jQuery('#'+alterId).append(html);
        jQuery('#'+alterId+'_num').val(num);
        
        return;
    },
    del_submit:function() {
		var numCur = jQuery('.checkall:checked').length, msg = 'Bạn có chắc chắn muốn xoá '+ numCur + ' tỉnh thành không!';
		if(numCur > 0) {
			shop.confirm(msg, function(){
				document.ListProvinceForm.method = 'POST';
				document.ListProvinceForm.submit();
			});
		}
		else {
			alert("Bạn phải chọn ít nhất 1 tỉnh thành để xóa");
		}
		
	}
};