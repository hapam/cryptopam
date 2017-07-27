<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:arial;font-size:14px;color:#000">
    <tbody>
    <tr>
        <td width="100%" style="background:#7A4418" align="center">
            <table cellpadding="0" cellspacing="0" border="0" width="705">
                <tbody>
                <tr>
                    <td align="right" width="100%">
                        <table cellpadding="0" cellspacing="0" border="0" style="margin:15px 5px 5px;font-size:13px">
                            <tbody>
                            <tr>
                                <td><a href="{$WEB_ROOT}" style="text-decoration:none;color:#fff" target="_blank">{$site_name}</a></td>
                                <td style="padding:0 10px 5px;color:#ccc;">|</td>
                                <td><a href="{$WEB_ROOT}lien_he.html" style="text-decoration:none;color:#fff" target="_blank">Liên hệ</a></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#fff;border:1px solid #72BF44;border-top:0;border-bottom:0">
                            <tbody>
                            <tr>
                                <td width="100%" style="padding:10px 20px 0;text-align:justify;">
                                    <p style="padding:0;margin:0 0 10px" align="center">
                                        <a href="{$WEB_ROOT}" target="_blank" style="text-decoration:none">
                                            <img src="cid:logo" width="{$logo.width}" height="{$logo.height}" border="0" alt="logo image" />
                                        </a>
                                    </p>
                                    <div>
                                        <div style="color: #333;font-size: 13px">Xin chào <b>{$user.fullname}</b>,</div>
                                        <div style="margin: 15px 0;">
                                            Nếu bạn chưa cài ứng dụng lấy mã OTP để truy cập vào Rento, vui lòng truy cập bằng điện thoại và cài ứng dụng theo các link sau:
                                        </div>
                                        <div style="line-height: 30px">
                                            <div style="margin-bottom: 15px"><a style="text-decoration: none" target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"><img style="vertical-align: middle" src="https://static9.zamba.vn/thumb_wl/30/rento/gallery/npsqv.png"/> Google Authenticator</a></div>
                                            <div style="margin-bottom: 15px"><a style="text-decoration: none" target="_blank" href="https://www.microsoft.com/en-us/store/apps/authenticator/9nblggh08h54"><img style="vertical-align: middle" src="https://static9.zamba.vn/thumb_wl/30/rento/gallery/9y1ab.png"/> Authenticator+</a></div>
                                            <div style="margin-bottom: 15px"><a style="text-decoration: none" target="_blank" href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8"><img style="vertical-align: middle" src="https://static9.zamba.vn/thumb_wl/30/rento/gallery/7yl9b.png"/> Google Authenticator</a></div>
                                        </div>
                                        <div style="margin:20px 0 10px">Sau khi cài ứng dụng vui lòng sử dụng tính năng quét mã <b>QR CODE</b> để lấy mã <b>OTP</b></div>
                                        <div style="margin: 15px auto;width: 200px">
                                            <img width="200" src="{$code.qrCodeUrl}"/>
                                        </div>
                                    </div>
                                    <div style="font-style: italic;margin:40px 0 0">
                                        Nếu bạn cần hỗ trợ khẩn cấp, vui lòng liên hệ trực tiếp với {$site_name}:<br />
                                        Hotline: {$support_city.hotline}<br />
                                        E-mail: <a href="mailto:{$support_city.email}">{$support_city.email}</a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:20px 0 0" align="center">
                                    <p style="color:#808080;font-size:13px;padding:0 20px 10px;margin:0" align="left">(<span style="color:red">*</span>) Đây là email hệ thống gửi tự động, vui lòng không trả lời (reply) lại email này.</p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="font-family:Tahoma;font-size:11px;color:#fff">
                        <p style="margin:0;padding:20px 0">
                            Copyright © 2015. All rights reserved<br />
                            <b>Địa chỉ:</b> {$support_city.address}<br />
                            <b>Điện thoại:</b> {$support_city.hotline} / <b>Fax:</b> {$support_city.fax}
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>