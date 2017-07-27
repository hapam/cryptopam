shop.browse = {
	appendToCKEditor:function(id, instanceIndex, func) {
        if (!window.opener) return;
        var img = document.getElementById(id).value;
        window.opener.CKEDITOR.tools.callFunction(func, img);
        window.close();
    },
	category:{
		add:function(edit){
			shop.show_overlay_popup('upload-image-cat', 'Tạo Album mới', shop.browse.theme.categoryForm(edit),
			{
				content: {
					'width' : '400px',
					'padding': '0px'
				}
			});
		},
		submit:function(id){
			if(id == 1){
				shop.hide_overlay_popup('upload-image-cat');
				shop.show_popup_message('Bạn không thể sửa thư mục mặc định', 'Thông báo lỗi', -1);
			}else{
                var action = (id && id >0) ? 'edit' : 'add',
                    title = jQuery('#upload-form-title-cat').val(),
                    des = jQuery('#upload-form-description').val();
                if(shop.is_blank(title)){
                    shop.raiseError('#upload-form-title-cat','Nhập tiêu đề',true);
                    return;
                }
                shop.closeErr('#upload-form-title-cat');
                shop.ajax_popup('act=gallery&code=category',"POST", {title: title, description: des, action: action, id: id},
                function(j){
                    shop.hide_loading();
                    shop.hide_overlay_popup('upload-image-cat');
                    if(j.err == 0){
                        shop.reload();
                    }
                    else {
                        alert(j.msg);
                    }
                });
			}
		},
		refresh:function(id){
			shop.ajax_popup('act=gallery&code=cat-refresh',"POST", {id: id},
                function(j){
                    shop.hide_loading();
                    if(j.err == 0){
                        shop.reload();
                    }
                    else {
                        alert(j.msg);
                    }
                });
		}
	},
	image:{
		upload:function(id){
			shop.show_overlay_popup('upload-image', (id > 0 ? 'Sửa ảnh' : 'Upload ảnh'), shop.browse.theme.uploadForm(id),
			{
			  content: {'width' : '400px', 'padding' : '0px'}
			});
			shop._store.variable['ajax-running'] = false;
		},
		submit:function(id){
			if(shop._store.variable['ajax-running']){
				//thong bao ajax dang chay
			}else{
				if(jQuery('#upload-form-title').val() == ''){
					shop.raiseError('#upload-form-title','Nhập tiêu đề',true);
					return false;
				}
				var ext = jQuery('#new_image').val().split('.').pop().toLowerCase();
				if(ext == '' && id > 0){
				}else if(jQuery.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
					alert('Chỉ được up định dạng gif, png, jpg, jpeg!');
					return false;
				}

				shop.closeErr('#upload-form-title');
				var action = (id && id >0) ? 'edit' : 'add',
				data = {myaction: action, id: id};
				data[''+BASE_TOKEN_NAME] = shop.getCSRFToken();

				jQuery('#upload-image-form').ajaxSubmit({
					beforeSubmit:function(){
						shop.show_loading();
						shop._store.variable['ajax-running'] = true;
					},
					data: data,
					dataType: 'json',
					success:function(j){
						shop.hide_loading();
						shop._store.variable['ajax-running'] = false;
						shop.hide_overlay_popup('upload-image');
						jQuery('#upload-image-form').remove();
						if(j.err == 0){
							shop.reload();
						}else{
							alert(j.msg);
						}
					}
				});
			}
			return false;
		}
	},
	theme:{
		uploadForm:function(id){
			var	data = {title:'', old_file:'', id: 0, cat: jQuery('#category-cur').val(), sort:0};
			if(id){
				data = {
					title: jQuery('#title'+id).val(),
					sort: jQuery('#sort'+id).val(),
					old_file: jQuery('#img'+id).val(),
					id: id
				};
			}
			return shop.join
			('<form id="upload-image-form" enctype="multipart/form-data" method="POST" action="'+BASE_URL+'ajax.php?act=gallery&code=upload">')
				('<div id="popup-form">')
					('<table id="pass-changed" border="0" cellpadding="8" cellspacing="0" align="center" width="100%">')
						('<tr>')
							('<td width="70">Tiêu đề</td>')
							('<td><input type="text" name="title" size="30" id="title" value="'+data.title+'" /></td>')
						('</tr>')
						('<tr>')
							('<td colspan="2"><div>&nbsp;<input type="hidden" name="cat" value="'+data.cat+'" /></div></td>')
						('</tr>')
						('<tr>')
							('<td width="70">Sắp xếp</td>')
							('<td class="onlyNums"><input type="text" name="sort" size="10" id="sort" value="'+data.sort+'" /><em style="color: red">(Số nhỏ lên trên)<em></td>')
						('</tr>')
						('<tr>')
							('<td colspan="2"><div>&nbsp;</div></td>')
						('</tr>')
						('<tr>')
							('<td style="vertical-align:top">Chọn ảnh</td>')
							('<td>')
								('<input type="file" name="image" size="30"  id="new_image"/>')
								('<input type="hidden" name="old_image" value="'+data.old_file+'" />')
							('</td>')
						('</tr>')
					('</table>')
				('</div>')
				('<div class="popup-footer" align="right">')
					('<button type="button" onclick="shop.browse.image.submit('+data.id+')">'+(id?'Cập nhật':'Thêm mới')+'</button>')
					('<button type="button" onclick="shop.hide_overlay_popup(\'upload-image\');">Hủy bỏ</button>')
				('</div>')
			('</form>')();
		},
		categoryForm:function(edit){
			var data = {title:'', description: '', id: 0};
			if(edit){
				var obj = shop.get_ele('gallery-category');
				data = {
					title: obj.options[obj.selectedIndex].text,
					description: obj.options[obj.selectedIndex].title,
					id: obj.options[obj.selectedIndex].value
				};
			}
			return shop.join
			('<div id="popup-form">')
				('<table id="pass-changed" border="0" cellpadding="8" cellspacing="0" align="center" width="100%">')
					('<tr>')
						('<td width="55">Tiêu đề</td>')
						('<td><input type="text" name="title" id="upload-form-title-cat" size="30" value="'+data.title+'" /></td>')
					('</tr>')
					('<tr>')
						('<td style="vertical-align:top">Mô tả</td>')
						('<td><textarea name="description" id="upload-form-description" rows="10" cols="46">'+data.description+'</textarea></td>')
					('</tr>')
				('</table>')
			('</div>')
			('<div class="popup-footer" align="right">')
				('<button type="button" onclick="shop.browse.category.submit('+data.id+')">Hoàn thành</button>')
				('<button type="button" onclick="shop.hide_overlay_popup(\'upload-image-cat\');">Hủy bỏ</button>')
			('</div>')();
		}
	}
};