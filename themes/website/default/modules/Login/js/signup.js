shop.signup = {
	submit:function(){
		var uname = jQuery('#reg_username').val(),
		email = jQuery('#reg_email').val(),
		pass = jQuery('#reg_pass').val(),
		pass2 = jQuery('#reg_pass2').val();

		jQuery('#reg_username').parent().removeClass('has-error');
		jQuery('#reg_email').parent().removeClass('has-error');
		jQuery('#reg_pass').parent().removeClass('has-error');
		jQuery('#reg_pass2').parent().removeClass('has-error');
		
		if(uname.length < 3){
			alert('Username be at least 3 characters!');
			jQuery('#reg_username').parent().addClass('has-error');
			jQuery('#reg_username').focus();
		}else if(!shop.is_email(email)){
			alert('Email is invalid!');
			jQuery('#reg_email').parent().addClass('has-error');
			jQuery('#reg_email').focus();
		}else if(pass.length < 6){
			alert('Password be at least 6 characters!');
			jQuery('#reg_pass').parent().addClass('has-error');
			jQuery('#reg_pass').focus();
		}else if(pass2 != pass){
			alert('Retype password not match!');
			jQuery('#reg_pass2').parent().addClass('has-error');
			jQuery('#reg_pass2').focus();
		}else{
			shop.ajax_popup('act=signup&code=reg', "GET", {uname: uname, email: email, password: pass},function (j) {
				if (j.err == 0) {
					alert("Welcome! Register successsfull");
					shop.redirect(j.url);
				}else{
					var msg = '';
					if(j.email != ''){
						msg += j.email + '\n';
						jQuery('#reg_email').parent().addClass('has-error');
					}
					if(j.uname != ''){
						msg += j.uname + '\n';
						jQuery('#reg_username').parent().addClass('has-error');
					}
					if(j.error != ''){
						msg += j.error + '\n';
					}
					alert(msg);
				}
			});
		}
	}
};