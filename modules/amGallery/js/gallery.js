shop.gallery = {
	getBrowseURL:function(){
		return BASE_URL+'/admin/gallery.html';
	},
	category:{
		conf:{data:null, page: 1, recPerPage: 20, total: 0, totalPage: 1, show: 2},
		pager:{
			changeRecPerPage:function(rec){
				shop.gallery.category.conf.recPerPage = rec;
				shop.cookie.set('gallery-rec', rec, 86400*7*4*12);
				shop.gallery.category.getImages(1);
			},
			total:function(data){
				if(data){
					shop.gallery.category.conf.data = data;
					shop.gallery.category.conf.total = data.length;
					if(shop.gallery.category.conf.total > 0){
						shop.gallery.category.conf.totalPage = Math.ceil(shop.gallery.category.conf.total / shop.gallery.category.conf.recPerPage);
						if(shop.gallery.category.conf.page > shop.gallery.category.conf.totalPage){
							shop.gallery.category.conf.page = shop.gallery.category.conf.totalPage;
						}
					}
				}
			},
			go:function(page){
				page = page > 0 ? page : 1;
				page = page <= shop.gallery.category.conf.totalPage ? page : shop.gallery.category.conf.totalPage;
				
				var from = (page-1)*shop.gallery.category.conf.recPerPage,
				to = from + shop.gallery.category.conf.recPerPage,
				data=[];

				if(to > shop.gallery.category.conf.total){
					to = shop.gallery.category.conf.total;
				}

				for(var i=from;i<to;i++){
					data[data.length] = shop.gallery.category.conf.data[i];
				}
				shop.gallery.category.conf.page = page;

				jQuery('#gallery').html(shop.gallery.theme.listImage(data));
				if(shop.gallery.category.conf.totalPage > 1){
					jQuery('.gallery-pager').html(shop.gallery.theme.pager());
				}else{
					jQuery('.gallery-pager').html('');
				}

                //===== Image gallery control buttons =====//
                jQuery(".gallery ul li div").hover(
                    function() { jQuery(this).children(".actions").fadeIn(200); },
                    function() { jQuery(this).children(".actions").fadeOut(200); }
                );

				//===== SORT ABLE =====//
				jQuery(".gallery ul").sortable().bind('sortupdate', function(e, item) {
					shop.gallery.image.changePos(item);
				});

                shop.gallery.category.changeWindow(page);
			}
		},
        changeCat: function (cat_id) {
			jQuery('.coverImg').html('');
            shop.gallery.category.getImages();
            shop.updateScriptData();
        },
        changeWindow: function (page) {
            var url = document.URL,
                cat_id = jQuery('#gallery-category').val();

            if(url.indexOf('cat=') > -1){
                url = url.replace(/cat=[0-9]+/gi, "cat="+cat_id);
            }else{
                url += '?cat='+cat_id;
            }
            if(url.indexOf('page=') > -1){
                url = url.replace(/page=[0-9]+/i, "page="+page);
            }else{
                url += '&page='+page;
            }
            window.history.pushState({state:cat_id}, document.title, url);
            jQuery('#title-album').html(jQuery('#gallery-category option:selected').text());
        },
		add:function(edit){
			shop.show_overlay_popup('upload-image-cat', 'Tạo chuyên mục', shop.gallery.theme.categoryForm(edit),
			{
			  content: {
				'width' : '400px'
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
						if(id > 0){
							jQuery('#gallery-category option:selected').html(title);
							if(shop.is_exists(jQuery.uniform)){
								jQuery.uniform.update('#gallery-category');
							}
							if ($.fn.selectpicker) {
								$('#gallery-category').selectpicker('refresh');
							}
						}else{
							jQuery('#gallery-category').append('<option value="'+j.data.id+'">'+title+'</option>')
							jQuery('#gallery-category').val(j.data.id);
							if(shop.is_exists(jQuery.uniform)){
								jQuery.uniform.update('#gallery-category');
							}
							if ($.fn.selectpicker) {
								$('#gallery-category').selectpicker('refresh');
							}
							
							//thay doi url
							shop.gallery.category.changeWindow(j.data.id);
							
							//load lai du lieu anh
							jQuery('.gallery-pager').html('');
							jQuery('#gallery').html('<ul></ul>');
							
							//cau hinh lai upload
							shop.updateScriptData();
						}
                    }
                    else {
                        alert(j.msg);
                    }
                });
			}
		},
		remove:function(){
			shop.confirm('Bạn muốn xóa danh mục '+ jQuery('#gallery-category option:selected').text() +' ?', function(){
				var id = jQuery('#gallery-category').val();
				if(id == 1){
					shop.show_popup_message('Bạn không thể xóa thư mục mặc định', 'Thông báo lỗi', -1);
				}else if(id > 1){
					shop.ajax_popup('act=gallery&code=remove-cat','GET',{id:id},
					function(j){
						if(j.err == 0){
                            var x = document.getElementById("gallery-category");
                            x.remove(x.selectedIndex);
                            jQuery('#gallery-category').val(1);
							if(shop.is_exists(jQuery.uniform)){
								jQuery.uniform.update('#gallery-category');
							}
							if ($.fn.selectpicker) {
								$('#gallery-category').selectpicker('refresh');
							}
                            shop.gallery.category.getImages(1);
						}else{
							shop.show_popup_message(j.msg, 'Thông báo lỗi', -1);
						}
					});
				}
			});
		},
		getImages:function(page){
			shop.ajax_popup('act=gallery&code=load-images&id='+jQuery('#gallery-category').val(),'GET',{},
			function(j){
				if(j.err == 0 && j.data.length > 0){
					shop.gallery.category.pager.total(j.data);
					shop.gallery.category.pager.go(page);
				}
				else {
					jQuery('#gallery').html("<ul></ul>");
					jQuery('.gallery-pager').html('');
                    shop.gallery.category.changeWindow(1);
				}
			});
		}
	},
	image:{
		changePos:function(item){
			var curItem = jQuery(".gallery ul li:nth-child("+(item.now+1)+")"), nextCurItem, type = 'left';
			if(item.last < item.now){
				type = 'right';
				nextCurItem = jQuery(".gallery ul li:nth-child("+(item.now)+")");
			}else{
				nextCurItem = jQuery(".gallery ul li:nth-child("+(item.now+2)+")");
			}
			shop.ajax_popup('act=gallery&code=change-pos','POST',{id: curItem.attr("data-id"), next: nextCurItem.attr("data-id"), type: type},
			function(j){
				if(j.err == 0){}
				else {
					alert("Không thay đổi được vị trí");
				}
			});
		},
		upload:function(id){
			shop.show_overlay_popup('upload-image', (id > 0 ? 'Sửa ảnh' : 'Upload ảnh'), shop.gallery.theme.uploadForm(id),
			{
			  content: {'width' : '400px'}
			});
			shop._store.variable['ajax-running'] = false;
			var cat = jQuery((id && id > 0) ? jQuery('#cat'+id) : '#gallery-category').val();
			jQuery('#upload-form-cat').val(cat);
			if(shop.is_exists(jQuery.uniform)){
				jQuery("#upload-form-cat, #new_image").uniform();
			}
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
					data:data,
					dataType: 'json',
					success:function(j){
						shop.hide_loading();
						shop._store.variable['ajax-running'] = false;
						shop.hide_overlay_popup('upload-image');
						jQuery('#upload-image-form').remove();
						if(j.err == 0){
							var nowCat = jQuery('#gallery-category').val();
							if(action == 'edit'){
								if(nowCat != j.data.cat_id){
									jQuery('#image-item'+id).remove();
								}else{
									var update = shop.gallery.theme.image(j.data);
									jQuery('body').append('<div class="hidden" id="image-update">'+update+'</div>');
									jQuery('#image-item'+id).html(jQuery('#image-update > .image-item').html());
									jQuery('#image-update').remove();
									
									//===== Image gallery control buttons =====//
									jQuery(".gallery ul li div").hover(
										function() { jQuery(this).children(".actions").fadeIn(200); },
										function() { jQuery(this).children(".actions").fadeOut(200); }
									);
									
									//===== SORT ABLE =====//
									jQuery(".gallery ul").sortable().bind('sortupdate', function(e, item) {
										shop.gallery.image.changePos(item);
									});
								}
							}else if(action == 'add'){
								var curPage = 1;
								if(nowCat == j.data.cat_id){
									curPage = shop.gallery.category.conf.page;
								}else{
									jQuery('#gallery-category').val(j.data.cat_id);
									if(shop.is_exists(jQuery.uniform)){
										jQuery.uniform.update('#gallery-category');
									}
									if ($.fn.selectpicker) {
										$('#gallery-category').selectpicker('refresh');
									}
								}
								shop.gallery.category.getImages(curPage);
							}
							shop.hide_overlay_popup('upload-image');
						}else{
							alert(j.msg);
						}
					}
				});
			}
			return false;
		},
		cover:function(id, img){
			shop.confirm('Bạn muốn đặt ảnh này làm đại diện cho album?', function(){
				if(id > 0){
					shop.ajax_popup('act=gallery&code=cover','GET',
					{id:id},
					function(j){
						if(j.err == 0){
							shop.gallery.theme.image(j.data);
						}else{
							shop.show_popup_message(j.msg, 'Thông báo lỗi', -1);
						}
					});
				}
			});
		},
		remove:function(id, img){
			shop.confirm('Bạn muốn xóa ảnh này?', function(){
				if(id > 0){
					shop.ajax_popup('act=gallery&code=remove-item','GET',
					{id:id, img:img, cat_id:jQuery('#gallery-category').val()},
					function(j){
						if(j.err == 0){
							jQuery('#image-item'+id).remove();
						}else{
							shop.show_popup_message(j.msg, 'Thông báo lỗi', -1);
						}
					});
				}
			});
		},
		showLink:function(id, img){
			shop.show_overlay_popup('img-show-info', 'Thông tin ảnh', shop.gallery.theme.shareImage(id, img),{
				content:{'width' : '650px'}
			});
		}
	},
	theme:{
		uploadForm:function(id){
			var cat = jQuery('#gallery-category').html(),
			data = {title:'', old_file:'', id: 0, cat: 1, sort:999};
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
							('<td width="70">Danh mục</td>')
							('<td><select name="cat" id="upload-form-cat">'+cat+'</select></td>')
						('</tr>')
						('<tr>')
							('<td colspan="2"><div>&nbsp;</div></td>')
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
					('<button type="button" onclick="shop.gallery.image.submit('+data.id+')">'+(id?'Cập nhật':'Thêm mới')+'</button>')
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
						('<td><textarea name="description" id="upload-form-description" rows="10" cols="40">'+data.description+'</textarea></td>')
					('</tr>')
				('</table>')
			('</div>')
			('<div class="popup-footer" align="right">')
				('<button type="button" onclick="shop.gallery.category.submit('+data.id+')">Hoàn thành</button>')
				('<button type="button" onclick="shop.hide_overlay_popup(\'upload-image-cat\');">Hủy bỏ</button>')
			('</div>')();
		},
		listImage:function(data){
			var	html = '';
			for(var i=0;i<data.length;i++){
				html += shop.gallery.theme.image(data[i]);
			}
			html = '<ul>' + html + '</ul>';
			return html;
		},
		shareImage:function(id, linkImage){
			return shop.join
			('<div class="pop-gallery mTop10">')
				('<div align="center"><img src="'+linkImage+'" /></div>')
				('<div id="popup-form" align="center">')
					('<table>')
						('<tbody>')
							('<tr>')
								('<td style="width: 100px;">Link Ảnh</td>')
								('<td><input type="text" value="'+ linkImage +'" onfocus="this.select();" style="width:500px" /></td>')
							('</tr>')
							('<tr>')
								('<td>Ảnh gốc</td>')
								('<td><input type="text" value="'+ shop.gallery.theme.imageSize(linkImage, 0) +'" onfocus="this.select();" style="width:500px" /></td>')
							('</tr>')
							('<tr>')
								('<td>HTML Code</td>')
								('<td><input type="text" value=\'<a href="'+linkImage+'" target="_blank"><img src="'+linkImage+'" \/><\/a>\' onfocus="this.select();" style="width:500px" /></td>')
							('</tr>')
						('</tbody>')
					('</table>')
				('</div>')
			('</div>')();
		},
		image:function(data){
			if(data.is_cover == 1){
				jQuery('.coverImg').html('<img src="'+data.image_sm+'" height="80" style="border:1px;padding:2px;background:#fefefe;float:right" />');
			}
			return shop.join
			('<li class="image-item" id="image-item'+data.id+'" data-id="'+data.id+'">')
				('<div><a class="lightbox" title="'+data.title+'" href="'+data.img+'"><img src="'+BASE_URL+'css/images/blank.gif" style="background-image:url('+data.image_sm+');background-size:cover" /></a>')
				('<div class="actions" style="display: none;">')
					('<a href="javascript:void(0)" onclick="shop.gallery.image.cover('+data.id+')" title="Ảnh đại diện"><img alt="Cover" src="'+BASE_URL+'modules/amGallery/css/images/cover.png" /></a>')
					('<a href="javascript:void(0)" onclick="shop.gallery.image.showLink('+data.id+',\''+data.image+'\')" title="Xem & Share"><img alt="Xem" src="'+BASE_URL+'modules/amGallery/css/images/preview.png" /></a>')
					('<a href="javascript:void(0)" onclick="shop.gallery.image.upload('+data.id+')" title="Sửa ảnh"><img alt="Sửa" src="'+BASE_URL+'modules/amGallery/css/images/update.png" /></a>')
					('<a href="javascript:void(0)" onclick="shop.gallery.image.remove('+data.id+', \''+data.img+'\')" title="Xóa ảnh"><img alt="Xóa" src="'+BASE_URL+'modules/amGallery/css/images/delete.png" /></a>')
					('<input type="hidden" value="'+data.title+'" id="title'+data.id+'" />')
					('<input type="hidden" value="'+data.img+'" id="img'+data.id+'" />')
					('<input type="hidden" value="'+data.cat_id+'" id="cat'+data.id+'" />')
					('<input type="hidden" value="'+data.sort+'" id="sort'+data.id+'" />')
				('</div></div>')
			('</li>')();
		},
		imageSize:function(link, size){
			size = size ? size : 0;
			link = link.split(IMG_URL);
			link = link[1].split('/');
			if(link.length == 6){
				link[4] = (size == 0) ? 'origin' : ('size'+size);
				return IMG_URL+link[0]+'/'+link[1]+'/'+link[2]+'/'+link[3]+'/'+link[4]+'/'+link[5];
			}
			return '';
		},
		pager:function(){
			var html = '', page = shop.gallery.category.conf.page, total = shop.gallery.category.conf.totalPage,
			show = shop.gallery.category.conf.show,
			from = page - show,
			to = page + show + 1;
			if(shop.gallery.category.conf.total > 0){
				html = '<div class="tPages"><ul class="pages">';
				if(page > 1 && total > 1){
					html += '<li class="prev"><a href="javascript:void(0)" onclick="shop.gallery.category.pager.go('+(page-1)+')">&nbsp;<&nbsp;</a></li>';
				}
				if(from > 1){
					html += '<li><a href="javascript:void(0)" onclick="shop.gallery.category.pager.go(1)">1</a></li>';
					if(from > 2){
						html += '<li>...</li>';
					}
				}else{
					from = 1;
				}
				if(to >= total){
					to = total;
				}
				for(var i=from;i<=to;i++){
					html += '<li><a class="'+(i==page?' active':'')+'" href="javascript:void(0)" onclick="shop.gallery.category.pager.go('+i+')">'+i+'</a></li>';
				}
				if(page + show + 1 < total){
					if(to < total-1){
						html += '<li>...</li>';
					}
					html += '<li><a href="javascript:void(0)" onclick="shop.gallery.category.pager.go('+total+')">'+total+'</a></li>';
				}
				if(page != total && total > 1){
					html += '<li class="next"><a href="javascript:void(0)" onclick="shop.gallery.category.pager.go('+(page+1)+')">&nbsp;>&nbsp;</a></li>';
				}
				html += '</ul></div>';
			}
			return html;
		}
	}
};

//auto load when document ready
shop.ready.add(function(){
	shop.multiupload();
	
	var recperpage = shop.cookie.get('gallery-rec');
	if(recperpage > 0){
		shop.gallery.category.conf.recPerPage = parseInt(recperpage);
	}

	//g_page defined in Gallery.tpl
	shop.gallery.category.getImages(g_page);
}, true);
