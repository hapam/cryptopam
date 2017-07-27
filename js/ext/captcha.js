shop.captcha = {
  img_id:'captcha',
  txt_id:'captcha_txt',
  size:{w:80,h:28},
  getCaptcha:function(cont_id){
	if(cont_id){
	  var html = shop.join
	  ('<div>')
		('<input type="text" id="'+shop.captcha.txt_id+'" name="'+shop.captcha.txt_id+'" class="fl" />')
		('<img src="'+shop.captcha.imgSrc()+'" class="fl mLeft10" id="'+shop.captcha.img_id+'" width="'+shop.captcha.size.w+'" height="'+shop.captcha.size.h+'" />')
		('<a href="javascript:void(0)" onclick="shop.captcha.reloadCaptcha()" class="fl mLeft10">Lấy ảnh khác</a>')
		('<div class="c"></div>')
	  ('</div>')();
	  jQuery('#'+cont_id).html(html);
	}
  },
  imgSrc:function(){
	return BASE_URL + 'captcha.php?w='+shop.captcha.size.w+'&h='+shop.captcha.size.h+'&l=5&r='+Math.random();
  },
  reloadCaptcha:function(){
	var captcha = shop.get_ele(shop.captcha.img_id);
	if(captcha){
	  captcha.src = shop.captcha.imgSrc();
	}
  },
  validCaptcha:function(cb){
	var captcha = shop.get_ele(shop.captcha.txt_id);
	if(captcha){
	  if(shop.util_trim(captcha.value) != ''){
		shop.ajax_popup("act=index&code=valid-captcha",'POST', {str: captcha.value},
		  function(json){
			if(json.err == 0){
			  if(cb) cb()
			}else{
			  switch(json.msg){
				case 1: json.msg = 'Captcha chưa được khởi tạo'; break;
				case 2:
				  json.msg = 'Sai captcha quá nhiều, vui lòng nhập mã khác';
				  shop.captcha.reloadCaptcha();
				  break;
				case 3: json.msg = 'Captcha đã hết hạn! Vui lòng nhập mã khác';
				  shop.captcha.reloadCaptcha();
				  break;
				case 4: json.msg = 'Sai captcha! Vui lòng nhập lại'; break;
			  }
			  alert(json.msg);
			}
		  }
		);
	  }else{
		alert('Vui lòng nhập captcha để tiếp tục');
		captcha.focus();
	  }
	}else{
	  alert('Không tồn tại ô nhập captcha');
	}
  }
};