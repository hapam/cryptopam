shop.ready.add(function(){
	jQuery('#password').keyup(function(){
		if(this.value != ''){
			var msg = passwordStrength(this.value,jQuery('#username').val());
			shop.raiseError('#password',msg,true,true);
		}
	}).blur(function(){
		var pass1 = jQuery('#password1').val();
		if(pass1 != ''){
			var pass = jQuery('#password').val();
			if(pass1 != pass){
				shop.raiseError('#password1',"Mật khẩu không trùng khớp", true);
			}else{
				shop.raiseError('#password1',"Oke",true,true);
			}
		}
	});
	
	jQuery('#password1').keyup(function(){
		if(this.value!= ''){
			var pass = jQuery('#password').val();
			if(pass != this.value){
				shop.raiseError('#password1',"Mật khẩu không trùng khớp", true);
			}else{
				shop.raiseError('#password1',"Oke",true,true);
			}
		}
	});
	
	jQuery('#website').blur(function(){
		if(this.value !='' && !shop.is_link(this.value)){
			shop.raiseError('#website',"URL không hợp lệ", true);
		}else{
			shop.closeErr('#website');
		}
	});
},true);

var show = false;
shop.showChangePass = function(obj){
	show = !show;
	if(show){
		jQuery('#change_pass').addClass('changed').show();
	}else{
		jQuery('#change_pass').removeClass('changed').hide();
	}
};

shop.onFormSubmit = function(form){
	var fullname = shop.util_trim(jQuery('#fullname').val());
	var web = shop.util_trim(jQuery('#website').val());
	if(web != '' && !shop.is_link(web)){
		shop.raiseError('#website',"URL không hợp lệ", true);
		return false;
	}else{
		shop.closeErr('#website');
	}

	var check_email= false, check_pass = false, submit = true,
	email_now = jQuery.trim(jQuery('#old_email').val()),
	email = jQuery.trim(jQuery('#email').val());
	
	if( (email_now.toLowerCase() != email.toLowerCase()) &&  shop.checkEmail(email)){
		check_email= true;
	}

	if(jQuery('#change_pass').hasClass('changed')){
		//check password	
		var pass  	= jQuery.trim(jQuery('#password').val());
		var pass1	= jQuery.trim(jQuery('#password1').val());
		if((pass != '') || (pass1 != '')){
			if(shop.checkPassword(pass,pass1)){
				check_pass = false;
			}else{
				return false;
			}		
		}else{
			submit = false;
		}
	}

	//valid old password & email by ajax
	if(check_email || check_pass){
		submit = false;		
		var uid = jQuery('#user_id').val(),
		post = {uid:uid};
		if(check_email){
			post['email'] = email;
		}
		if(check_pass){
			post['old_pass'] = old_pass;
		}
		url = 'act=user&code=check_info';
		shop.ajax_popup(url,'POST',post,function(data){
			if(data.err == -1){
				for(var i in data){
					shop.raiseError('#'+i, data[i], true);
				}
			}
			else{
				form.submit();
			}
		});
	}
	if(submit){
		form.submit();
	}
	return true;
};

shop.checkPassword = function(pass, pass1){
	
	//check do an toan cua password
	var safe = jQuery('.pass_strong',jQuery('#password').parent()).attr('id');
	if(safe == 'pass_short' || safe == 'pass_bad'){
		shop.raiseError('#password', "Mật khẩu không an toàn", true);
		return false;
	}
	//check password
	if(pass == '' || pass == undefined){
		shop.raiseError('#password', "Chưa nhập mật khẩu mới", true);
		return false;
	}else{
		shop.closeErr('#password');
	}
	if(pass1 == '' || pass1 == undefined){
		shop.raiseError('#password1', "Chưa nhập lại mật khẩu", true);
		return false;
	}else if(pass != pass1){
		shop.raiseError('#password1', "Mật khẩu không trùng khớp", true);
		return false;
	}else{
		shop.closeErr('#password');
		shop.closeErr('#password1');
	}
	return true;
};

shop.checkEmail = function(email){
	if(email == undefined || email == null)
		email = jQuery.trim(jQuery('#email').val());

	if(email == '' || email == undefined){
		shop.raiseError('#email', "Chưa nhập email", true);
		return false;
	}else if(!shop.is_email(email)){
		shop.raiseError('#email', "Email không hợp lệ", true);
		return false;
	}else{
		shop.closeErr('#email');
	}
	return true;
};

shop.echoPassStrong = function(id, msg){
	jQuery(id).removeClass('error');
	var p = jQuery(id).parent();
	jQuery('.showErr',p).remove();
	p.append('<span class="pLeft5 showErr">'+msg+'</span>');
};

shop.reg_pass_press = function(obj){
	if(obj.value != ''){
		var msg = passwordStrength(obj.value,jQuery('#username').val());
		shop.echoPasswordStrong('#password', msg);
	}
};

shop.echoPasswordStrong = function(id, msg){
	var p = jQuery(id).parent();
	jQuery('.showErr',p).remove();
	p.append('<span class="pLeft5 showErr">'+msg+'</div>');
};