{literal}<script type="text/javascript">
	var RecaptchaOptions = {
		theme : 'custom',
		custom_theme_widget: 'recaptcha_widget'
	};
	shop.ready.add(function(){
		shop.enter('#user_name', shop.login_submit);
		shop.enter('#password', shop.login_submit);
	},true);
</script>{/literal}

<div class="login-box">
    <div class="logo">
        <a href="{$base_url}" target="_blank"><b>{$site_name}</b></a>
        <small> Đơn giản - Nhanh - Ổn định</small>
    </div>
    <div class="card">
        <div class="body">
            <form id="bm_login_form" method="post" onsubmit="return shop.login_submit();" name="bm_login_form" action="{$base_url}ajax.php?act=user&code=login_user">
                <div class="msg">Đăng nhập vào quản trị Website</div>
                <div class="input-group">
                    <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                    <div class="form-line">
                        <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Tên đăng nhập" required autofocus>
                    </div>
                </div>
                <div class="input-group">
                    <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                    <div class="form-line">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu" required>
                    </div>
                </div>
				{if $captcha == 1}
					<div class="row clearfix" id="recaptcha_widget" align="center">
						<div class="col-red">Thông tin đăng nhập không đúng!!!<br/>Vì lí do bảo mật, Captcha được kích hoạt</div>
						<div id="recaptcha_image" class="m-t-10 m-b-10"></div>
					</div>
					<div class="input-group">
						<span class="input-group-addon"><i class="material-icons">security</i></span>
						<div class="form-line">
							<input type="text" class="form-control" id="recaptcha_response_field" name="recaptcha_response_field" placeholder="Nhập captcha">
						</div>
						<span class="input-group-addon" style="cursor:pointer" title="Lấy ảnh khác"><i class="material-icons" onclick="Recaptcha.reload()">autorenew</i></span>
						<input type="hidden" id="captcha_active_check"  value="1" />
					</div>
					<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k={$public_key}"></script>
				{/if}
                <div class="row">
                    <div class="col-xs-7 p-t-5">
						{if $captcha != 1 && $log2step == 0}
                        <input type="checkbox" name="save_login" id="chk_save" class="filled-in chk-col-pink">
                        <label for="chk_save">Nhớ đăng nhập</label>
						{/if}
                    </div>
                    <div class="col-xs-5">
                        <button class="btn btn-block bg-pink waves-effect" type="button" onclick="shop.login_submit();">ĐĂNG NHẬP</button>
                    </div>
                </div>
                <div class="row m-t-15 m-b--20">
                    <div class="col-xs-6">
                        <a href="sign-up.html">Đăng kí ngay!</a>
                    </div>
                    <div class="col-xs-6 align-right">
                        <a href="forgot-password.html">Quên mật khẩu?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

