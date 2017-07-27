shop.login_submit = function() {
	var login_form = shop.get_ele('bm_login_form'),
	user = shop.util_trim(login_form['user_name'].value),
	pass = shop.util_trim(login_form['password'].value),
	eleNewPass = shop.get_ele('frmTodoChangePass'),
	newpass = '',
	save = login_form['save_login'],
	cookie = 'off';
	if(save){
		cookie = save.checked ? 'on' : 'off';
	}

	if (eleNewPass && jQuery('#frmTodoChangePass').is(':visible')) {
		newpass = shop.util_trim(eleNewPass['todo_new_password'].value);
		var newpass2 = shop.util_trim(eleNewPass['todo_new_password2'].value);
		if (newpass == pass) {
			swal('Mật khẩu mới phải khác mật khẩu cũ', '', 'error');
			jQuery('#todo_new_password').focus();
			return;
		}
		if (newpass.length < 6) {
			swal('Mật khẩu mới phải có ít nhất 6 ký tự', '', 'error');
			jQuery('#todo_new_password').focus();
			return;
		}
		
		if (newpass != newpass2) {
			swal('Mật khẩu mới nhập lại không khớp', '', 'error');
			jQuery('#todo_new_password2').focus();
			return;
		}
	} else {
		newpass = '';
	}
	if (shop.is_blank(user) || shop.is_blank(pass)) {
		return;
	} else {
		if(pass.length < 5){
			swal('Mật khẩu phải có ít nhất 5 ký tự trở lên', '', 'error');
			jQuery('#password').focus().select();
			return;
		}
		
		var captcha = 0;
		if(jQuery('#captcha_active_check').val() == 1){
			if(jQuery('#recaptcha_response_field').val() == ''){
				swal('Vui lòng nhập Captcha', '', 'error');
				jQuery('#recaptcha_response_field').focus().select();
				return;
			}
			captcha = 1;
		}
		
		if(shop._store.variable['ajax-running']){
			//thong bao ajax dang chay
		}else{
			var form_id = 'bm_login_form',
			data = {user: user,pass: pass,set_cookie: cookie,newpass: newpass, captcha: captcha};
			data[''+BASE_TOKEN_NAME] = shop.getCSRFToken();
			
			if (eleNewPass){
				form_id = 'frmTodoChangePass';
			}
			jQuery('#'+form_id).ajaxSubmit({
				beforeSubmit:function(){
					shop.show_loading();
					shop._store.variable['ajax-running'] = true;
				},
				data:data,
				dataType: 'json',
				success:function(j){
					shop.hide_loading();
					shop._store.variable['ajax-running'] = false;
					if (j.err == 0 && j.msg == 'success') {
						shop.redirect(j.url_next);
					} else {
						switch(j.msg){
							case 'captcha':
								j.msg = 'Sai mã Captcha';
								Recaptcha.reload();
								break;
							case 'un_active':j.msg = 'Tài khoản của bạn vẫn chưa được kích hoạt';break;
							case 'blocked'  :j.msg = 'Tài khoản của bạn đã bị khóa';break;
							case 'nodata'   :j.msg = 'Tài khoản hoặc mật khẩu không hợp lệ';break;
							case 'err_pass' :
							case 'err_user' :
								j.msg = (j.msg == 'err_pass') ? 'Sai mật khẩu' : 'Không tồn tại tên đăng nhập này';
								if(j.captcha == 1 && j.wrong >= j.number_error){
									shop.reload();
									return;
								}
								break;
							case 'other_new_pass': j.msg = 'Mật khẩu mới phải khác mật khẩu cũ';break;
							case 'invalid_new_pass': j.msg = 'Mật khẩu mới phải có tối thiểu 6 ký tự và khó đoán!';break;
							case 'change_pass':
								var html = shop.join
								('<form id="frmTodoChangePass" name="frmTodoChangePass" method="post" onsubmit="shop.login_submit(); return false;" action="'+ BASE_URL +'ajax.php?act=user&code=login_user">')
									('<div id="popup-form">')
										('<div style="color:green;line-height:20px;padding:0px 0px 10px">')
											('Để nâng cao tính an toàn, bảo mật của hệ thống, vui lòng đổi mật khẩu '+((j.first_login == 1) ? 'khi đăng nhập lần đầu tiên': 'sau mỗi khoảng thời gian <font color="red"><b>'+ j.day_change +' ngày</b></font>'))
											('<br />')
											(j.last != '' ? 'Lần đổi mật khẩu gần nhất của bạn là: <span style="color:red">' + j.last + '</span>':'<span></span>')
										('</div>')
										('<table id="pass-changed" border="0" cellpadding="8" cellspacing="0" align="center" width="100%">')
											('<tr>')
												('<td width="145">Mật khẩu mới</td>')
												('<td><input type="password" id="todo_new_password" name="todo_new_password" value="" /></td>')
											('</tr>')
											('<tr>')
												('<td>Nhập lại mật khẩu mới</td>')
												('<td><input type="password" id="todo_new_password2" name="todo_new_password2" value="" /></td>')
											('</tr>')
										('</table>')
									('</div>')
									('<div class="popup-footer" align="right">')
										('<button type="submit">Đổi mật khẩu</button>')
									('</div>')
								('</form>')();
								shop.show_overlay_popup('pop_changepass', 'Đổi mật khẩu', html,{content: {'width' : '500px','height' : 'auto'}});
								jQuery('#todo_new_password').focus();
								return;
						}
						swal(j.msg, '', 'error');
					}
				}
			});
		}
	}
	return;
};

