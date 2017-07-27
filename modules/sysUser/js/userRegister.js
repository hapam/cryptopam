shop.submitForm = function(frm){
	if(shop.validRegForm()){
		var uname = shop.util_trim(jQuery('#username').val());
		var email = shop.util_trim(jQuery('#email').val());
		shop.ajax_popup('act=user&code=valid_reg','POST',{uname:uname,email:email},function(json){
			if(json.uname || json.email){
				if(json.uname){
					shop.raiseError('#username',json.uname,true);
				}
				if(json.email){
					shop.raiseError('#email',json.email,true);
				}
			}else{
				frm.submit();
			}
		});
	}
};

shop.validRegForm = function(){
	var uname = shop.util_trim(jQuery('#username').val());
	if(uname == ''){
		shop.raiseError('#username', 'Chưa nhập tài khoản', true);
		return false;
	}else if(uname.length <3){
		shop.raiseError('#username', 'Tối thiểu 3 kí tự', true);
		return false;
	}else if(uname.length >50){
		shop.raiseError('#username', 'Tối đa 50 kí tự', true);
		return false;
	}else if(uname.match(/^[0-9]/)){
		shop.raiseError('#username', 'Phải bắt đầu bằng kí tự', true);
		return false;
	}else if(uname.search(/^[0-9_a-zA-Z]*$/) == -1){
		shop.raiseError('#username', 'Chỉ chấp nhận chữ, số, dấu _', true);
		return false;
	}else{
		shop.closeErr('#username');
	}
	
	var email = shop.util_trim(jQuery('#email').val());
	if(email == ''){
		shop.raiseError('#email', 'Chưa nhập email', true);
		return false;
	}else if(!shop.is_email(email)){
		shop.raiseError('#email', 'Email không hợp lệ', true);
		return false;
	}else{
		shop.closeErr('#email');
	}
	
	var pass = shop.util_trim(jQuery('#password').val());
	if(pass == ''){
		shop.raiseError('#password', 'Chưa nhập mật khẩu', true);
		return false;
	}else{
		//check do an toan cua password
		var safe = jQuery('.showErr',jQuery('#pass').parent()).attr('id');
		if(safe == 'pass_short' || safe == 'pass_bad'){
			raiseError('#password', "Mật khẩu không an toàn");
			return false;
		}else{
			shop.closeErr('#password');
		}
		
		var pass1 = shop.util_trim(jQuery('#password1').val());
		if(pass1 == ''){
			shop.raiseError('#password1', 'Chưa nhập lại mật khẩu', true);
			return false;
		}else if(pass != pass1){
			shop.raiseError('#password1', 'Mật khẩu không khớp', true);
			return false;
		}else{
			shop.closeErr('#password1');
		}
	}
	return true;
};

shop.echoPasswordStrong = function(id, msg){
	var p = jQuery(id).parent();
	jQuery('.showErr',p).remove();
	p.append('<span class="pLeft5 showErr">'+msg+'</div>');
};

shop.reg_uname_press = function(obj){
	var pass = jQuery('#password').val();
	if(pass != ''){
		var msg = passwordStrength(pass,obj.value);
		shop.echoPasswordStrong('#password', msg);
	}
};

shop.reg_pass_press = function(obj){
	if(obj.value != ''){
		var msg = passwordStrength(obj.value,jQuery('#username').val());
		shop.echoPasswordStrong('#password', msg);
	}
};