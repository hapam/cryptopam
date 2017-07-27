{if $check_startQR!=''}
<script type="text/javascript">
    shop.ready.add(function(){literal}{
        swal({{/literal}
            title: 'Đã kích hoạt đăng nhập 2 bước',
            type: 'success',
            html: true,
            text: shop.join("<div>Hãy kiểm tra email <b>{$check_startQR}</b></div>")
            ("<div style='margin:5px 0 0 0'>Thông tin hướng dẫn chi tiết đã được gửi vào Email</div>")(),
            showCloseButton: true
        {literal}});
    }{/literal}, true);
</script>{/if}

<div class="login-box">
    <div class="logo">
        <a href="{$base_url}" target="_blank"><b>{$site_name}</b></a>
        <small> Đơn giản - Nhanh - Ổn định</small>
    </div>
    <div class="card">
        <div class="body">
            <form id="bm_login_form" method="post" onsubmit="return shop.login_submit();" name="bm_login_form" action="{$base_url}ajax.php?act=user&code=login_user">
                <div class="msg">Đăng nhập 2 bước - Xác thực OTP</div>
                {$msg}
                <div class="input-group">
                    <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                    <div class="form-line">
                        <input type="text" class="form-control" name="token" id="token" placeholder="Mã OTP" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-7 p-t-5">
                        <input type="checkbox" name="save_login" id="chk_save" class="filled-in chk-col-pink" value="1">
                        <label for="chk_save">{if $log2step_time <= 0}Không hỏi lại{else}Không hỏi lại {$log2step_time} ngày{/if}</label>
                    </div>
                    <div class="col-xs-5">
                        <button class="btn btn-block bg-pink waves-effect" type="submit">XÁC THỰC</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>