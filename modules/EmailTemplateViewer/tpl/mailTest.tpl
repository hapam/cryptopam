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
                                        Kính chào <b>Quý khách</b>,<br />
                                        <div style="padding:10px 0 20px;line-height:25px;">
                                            Quý khách bấm vào link bên dưới để đặt lại mật khẩu mới: <br />
                                            <a href="{$customer.link}" style="text-decoration:none;" target="_blank"><b><font color="#1155DC">Đặt lại mật khẩu</font></b></a><br /><br />
                                            Nếu liên kết không hoạt động, copy và dán địa chỉ sau vào trình duyệt để thực hiện:<br/>
                                            <font color="#1155DC" style="text-decoration:none;">{$customer.link}</font>
                                        </div>
                                        <div style="padding:10px 0 0">
                                            Nếu Quý khách không yêu cầu việc đặt lại mật khẩu này, vui lòng bỏ qua nội dung của email này, lệnh sẽ được huỷ trong vòng 72 giờ.
                                        </div>
                                    </div>
                                    <div style="font-style: italic;margin:20px 0 0">
                                        Nếu Quý khách cần hỗ trợ khẩn cấp, vui lòng liên hệ trực tiếp với {$site_name}:<br />
                                        Hotline: {$support_city.hotline}<br />
                                        E-mail: <a href="mailto:{$support_city.email}">{$support_city.email}</a>
                                        <br /><br /><br />
                                        <b style="font-style: normal">{$site_name} rất hân hạnh được phục vụ Quý khách!</b>
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