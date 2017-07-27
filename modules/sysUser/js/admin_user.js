shop.user_submit = function() {
	var numCur = jQuery('.checkall:checked').length, msg = 'Bạn có chắc chắn muốn xoá '+ numCur + ' thành viên không!';
	if(numCur > 0) {
		shop.confirm(msg, function(){
			shop.admin.system.postMethod('listUserForm');
		});
	}
	else {
		alert("Bạn phải chọn ít nhất 1 thành viên để xóa");
	}
	
};
shop.login_as = function(id){
	shop.ajax_popup('act=user&code=login_as',"POST", {id:id},
	function (j) {
		if(j.err==0){
			window.location = j.url
		}
		else{
			if(j.msg == 'fail'){
				j.msg = "User không tồn tại hoặc đã bị khóa, xóa";
			}else if(j.msg == 'permiss'){
				j.msg = "Không có quyền đăng nhập vào account có quyền hạn lớn hơn account của bạn";
			}else{
				j.msg = "Lỗi không xác định";
			}
			shop.show_popup_message(j.msg, "Thông báo lỗi", -1);
		}
	});
};
	
shop.admin.user = {
	changePassword:function(id){
		var html = shop.join
		('<div id="popup-form">')
			('<table id="pass-changed" border="0" cellpadding="8" cellspacing="0" align="center" width="100%">')
				('<tr>')
					('<td width="115">Mật khẩu cũ</td>')
					('<td><input type="password" name="pop_old_pass" id="pop_old_pass" /></td>')
				('</tr>')
				('<tr>')
					('<td>Mật khẩu mới</td>')
					('<td><input type="password" name="pop_new_pass" id="pop_new_pass" /></td>')
				('</tr>')
				('<tr>')
					('<td>Nhập lại mật khẩu</td>')
					('<td><input type="password" name="pop_re_pass" id="pop_re_pass" /></td>')
				('</tr>')
			('</table>')
		('</div>')
		('<div class="popup-footer" align="right">')
			('<button type="button" onclick="shop.admin.user.doChangePassword()">Đổi mật khẩu</button>')
			('<button type="button" onclick="shop.hide_overlay_popup(\'user-change-pass\');">Hủy bỏ</button>')
		('</div>')();
		shop.show_overlay_popup('user-change-pass',"Đổi mật khẩu",html,{
			release:function(){
				jQuery('#pass-changed input').keydown(
					function(event){if (event.keyCode == 13) shop.admin.user.doChangePassword()}
				);
			}
		});
	},
	doChangePassword:function(){
		var pass = {
			old: jQuery('#pop_old_pass').val(),
			news: jQuery('#pop_new_pass').val(),
			valid: jQuery('#pop_re_pass').val()
		};
		if(pass.old == ''){
			shop.raiseError('#pop_old_pass','Chưa nhập mật khẩu cũ',true);
			return;
		}else{
			shop.closeErr('#pop_old_pass');
			if(pass.news == ''){
				shop.raiseError('#pop_new_pass','Chưa nhập mật khẩu mới',true);
				return;
			}else{
				shop.closeErr('#pop_new_pass');
				if(pass.valid == ''){
					shop.raiseError('#pop_re_pass','Chưa nhập lại mật khẩu mới',true);
					return;
				}else if(pass.valid != pass.news){
					shop.raiseError('#pop_re_pass','Mật khẩu mới không khớp',true);
					return;
				}else{
					shop.closeErr('#pop_re_pass');
				}
			}
		}
		shop.ajax_popup("act=user&code=change-pass","POST",pass,
		function(j){
			if(j.err == 0){
				shop.hide_overlay_popup('user-change-pass');
				shop.show_popup_message('Chúc mừng! Mật khẩu của bạn đã được thay đổi',"Đổi mật khẩu");
			}else{
				if(j.msg == 'old_error'){
					shop.raiseError('#pop_old_pass','Mật khẩu sai',true);
				}else if(j.msg == 'not_equal'){
					shop.raiseError('#pop_re_pass','Mật khẩu mới không khớp',true);
				}
			}
		});
	},
	changeActive:function(obj, id, status){
		shop.confirm("User này đang ở trạng thái "+(status == 0 ? 'chưa' : 'đã được')+" kích hoạt<br />Bạn có muốn thay đổi trạng thái của User này không ?",
		function(){
			shop.ajax_popup("act=user&code=active-user","POST",{is_active:status, id:id},
			function(j){
				if(j.err == -1){
					shop.show_popup_message(j.msg,"Thông báo lỗi", -1);
				}else{
					obj.innerHTML = '<img src="style/images/admin/icons/ok'+(j.active == 0 ? '_grey' : '')+'.png" width="16" height="16" />';
					obj.onclick = function(){
						shop.admin.user.changeActive(this,j.id,j.active);
					};
					shop.show_popup_message(j.msg,"Thành Công");
				}
			});
		});
	},
	sendQR:function(uid){
		shop.confirm('Bạn có chắc chắn gửi QR code ?', function(){
			shop.ajax_popup("act=user&code=send-QR","POST",{uid:uid},
			function(j){
				if(j.err == -1){
					shop.show_popup_message(j.msg,"Thông báo lỗi", -1);
				}else{
					alert('Đã gửi mã QR code thành công!', shop.reload);
				}
			});
		});
	},
	ignoreQR:function(uid, action){
		shop.ajax_popup("act=user&code=ignore-QR","POST",{uid:uid, status:action},
		function(j){
			if(j.err == -1){
				shop.show_popup_message(j.msg,"Thông báo lỗi", -1);
			}else{
				shop.reload();
			}
		});
	}
};