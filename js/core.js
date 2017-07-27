/*-----------------------------*/
/*        SHOPPING CORE		   */
/*-----------------------------*/
var shop = {
  _store : {
    ajax: {},
    method: {},
    variable: {},
    cache:{}
  },
  _all_popup: {},
  _show_popup : {},
  _active_popup:function(b,c,d,e){if(shop.is_exists(shop._all_popup[b])){var f=shop.get_ele(b);jQuery(f).remove()}var g={auto_hide:0,position:'default',pos_type:'absolute',type:'show-hide',esc:true,overlay:{'background-color':'#000','opacity':'0.8'},background:{'background-color':'#fff'},border:{'background-color':'rgba(255,255,255,0.24)','padding':'5px'},title:{'background-color':'#034b8a','color':'#fff','status':1,'display':'block'},content:{'width':'500px','height':'auto','padding':'20px','display':'block'},before:function(){},release:function(){},onclose:function(){}};if(shop.is_exists(e)){for(var o in e){if(!Object.prototype[o]&&shop.is_exists(e[o])){if(shop.is_func(e[o])){g[o]=e[o]}else if(shop.is_obj(e[o])){for(var i in e[o]){var h=e[o];if(!Object.prototype[i]&&shop.is_exists(h[i])){g[o][i]=h[i]}}}else{g[o]=e[o]}}}}shop._all_popup[b]=g.type;var j=jQuery(window).height();var k=jQuery(window).width();var l=jQuery(document).height();var m=jQuery(document).width();if(g.type=='overlay'){var n=jQuery('<div id='+b+' class="popup-overlay-bg"> </div>').css({'background-color':g.overlay['background-color'],'opacity':g.overlay['opacity'],'position':g.pos_type,'height':l}).appendTo('body')}else{var p=(g.title.status==1)?'blue':((g.title.status==-1)?'red':'orange'),q=jQuery('<div class="popup-close-button pcb-'+p+'-normal"></div>').mouseover(function(){this.className='popup-close-button pcb-'+p+'-hover'}).mouseout(function(){this.className='popup-close-button pcb-'+p+'-normal'}).click(function(){shop._hide_popup(b)}),r=jQuery('<div class="fl">'+c+'</div>'),s=jQuery('<div class="popup-title-bg"></div>').css({'display':g.title['display'],'color':g.title['color'],'background-color':g.title['background-color']}).append(q).append(r).append('<div class="c"></div>'),t=jQuery('<div id="popup-container"></div>').css({'font-size':shop.is_exists(g.content['font-size'])?g.content['font-size']:'14px','height':g.content['height'],'padding':g.content['padding'],'display':g.content['display']}),u=null,v=null;if(shop.is_str(d)){t.html(d)}else if(shop.is_ele(d)){u=d.id;v=d.style.display;t.append(d);d.style.display="block"}var w=jQuery('<div style="background-color: '+g.background['background-color']+'"></div>');var n=jQuery('<div id='+b+' class="'+g.type+' popup-content-container"></div>').css({'background-color':g.border['background-color'],'position':g.pos_type,'padding':g.border['padding'],'width':g.content['width']}).append(w.append(s).append(t)).appendTo('body').fadeTo("slow",1);if(u){shop.get_ele(b).content_popup={id:u,state:v}}g.before(n);switch(g.position){case'top-left':n.css({'top':0,'left':0});break;case'top-center':n.css({'top':0,'left':(m-n.width())/2});break;case'top-right':n.css({'top':0,'right':0});break;case'center-center':n.css({'top':(j-n.height())/2,'left':(m-n.width())/2});break;case'bottom-left':n.css({'bottom':0,'left':0});break;case'bottom-center':n.css({'bottom':0,'left':(m-n.width())/2});break;case'bottom-right':n.css({'bottom':0,'right':0});break;case'default':n.css({'top':shop.get_top_page()+92,'left':(m-n.width())/2});break}}if(g.auto_hide){setTimeout(function(){n.fadeTo('show',0,function(){if(g.type!='show-hide'){jQuery(this).remove()}else{jQuery(this).hide()}})},g.auto_hide)}shop.get_ele(b).onclose=g.onclose;g.release(n);if(g.esc&&b!='overlay-popup'){jQuery(document).keydown(function(a){if(a.keyCode==27){shop.hide_popup(b)}})}return n},
  _hide_popup:function(a){var b=shop.get_ele(a);if(shop.is_ele(b)){shop.hide_popup(b.overlay_popup);if(shop.is_exists(b.content_popup)){var c=shop.get_ele(b.content_popup.id);c.style.display=b.content_popup.state}if(shop._all_popup[a]=='one-time'||shop._all_popup[a]=='overlay'){shop._all_popup[a]=null;delete shop._all_popup[a];b.parentNode.removeChild(b)}else{b.style.display="none"}var d=b.onclose;if(shop.is_func(d)){d()}else if(shop.is_str(d)){eval(d)}}}
};

/*-----------------------------*/
/*        AJAX - POST - GET	   */
/*-----------------------------*/

//them co che cache, chong duplicate option = {cache: 'cache_key', duplicate: false}
shop.ajax_popup=function(url,method,param,callback,option){var data='',opt={loading:(shop.is_obj(option)&&shop.is_func(option.loading))?option.loading:shop.show_loading,error:(shop.is_obj(option)&&shop.is_func(option.error))?option.error:shop.hide_loading,cache:(shop.is_obj(option)&&shop.is_exists(option.cache))?option.cache:'',duplicate:(shop.is_obj(option)&&shop.is_exists(option.duplicate))?option.duplicate:false};if(!shop.is_exists(url)||(!opt.duplicate&&shop.is_exists(shop._store.ajax[url]))){return}if(shop.is_exists(callback)&&shop.is_exists(opt.cache)&&(opt.cache!='')){var fromCache=shop.cache.get(opt.cache,false);if(fromCache){callback(fromCache);return}}jQuery.ajax({beforeSend:function(){opt.loading();shop._store.ajax[url]=1},url:BASE_URL+'ajax.php?'+url+'&rand='+Math.random()+'&'+BASE_TOKEN_NAME+'='+shop.getCSRFToken(),type:method?method:'POST',data:param,dataType:'json',success:function(xhr){delete shop._store.ajax[url];shop.hide_loading();if(xhr&&shop.is_exists(xhr.intReturn)){switch(xhr.intReturn){case-1:shop.show_popup_message(xhr.msg,"Thông báo lỗi!",-1);break;case 0:shop.show_popup_message(xhr.msg,"Cảnh báo",0);break;case 1:shop.show_popup_message(xhr.msg,"Thông báo",1);break}}if(shop.is_exists(xhr.script)){eval(xhr.script)}if(shop.is_exists(callback)){if(shop.is_exists(opt.cache)&&(opt.cache!='')){shop.cache.set(opt.cache,xhr)}callback(xhr)}},error:function(xhr,textStatus,errorThrown){delete shop._store.ajax[url];opt.error();if(shop.is_obj(shop.rootPanel)&&shop.rootPanel.mode.debug==1){alert('Status:'+textStatus+'\n'+errorThrown)}}})};

//token key
shop.getCSRFToken = function() {if(document.domain!=DOMAIN_NAME){return '0102yuh'}return BASE_TOKEN};

//loading float container
shop.show_loading = function(txt){txt=shop.is_str(txt)?txt:'Đang tải dữ liệu...';jQuery('.float_loading').remove();jQuery('body').append('<div class="float_loading">'+txt+'</div>');jQuery('.float_loading').fadeTo("fast",0.9);shop.update_position();jQuery(window).scroll(shop.updatePosition)};
shop.update_position = function(){if(shop.is_ie()){jQuery('.mine_float_loading').css('top',document.documentElement['scrollTop'])}};
shop.hide_loading = function(){jQuery('.float_loading').fadeTo("slow",0,function(){jQuery(this).remove()})};

/*-----------------------------*/
/*        POPUP				   */
/*-----------------------------*/

shop.show_popup =  function(popup_id, title, content, option) {
  shop.hide_all_popup();
  shop._active_popup(popup_id, title, content, option);
};

shop.hide_popup = function(id) {shop._hide_popup(id)};

shop.show_next_popup = function(popup_id, title, content, option) {
  shop._active_popup(popup_id, title, content, option);
};

shop.hide_all_popup = function(popup_id) {
  for(var i in shop._all_popup) {
	if (Object.prototype[i]) continue;
	if(popup_id != i){
	  shop._hide_popup(i);
	}
  }
};

shop.show_overlay_popup = function(popup_id, title, content, option) {
  shop.hide_all_popup(popup_id);
  shop._active_popup('overlay-popup','','',{
	type: 'overlay',
	overlay: shop.is_exists(option) ? option.overlay : null
  });
  //chinh sua theo giao dien admin
  if(shop.isAdminUrl()){
	if(shop.is_exists(option)){
	  if(shop.is_exists(option.content)){
		option.content.padding = "0px";
	  }else{
		option.content = {padding:"0px"}
	  }
	}else{
	  option={content:{padding:"0px"}};
	}
  }
  shop._active_popup(popup_id, title, content, option);
  //store to remove;
  shop.get_ele(popup_id).overlay_popup = 'overlay-popup';
  //update height;
  shop.get_ele('overlay-popup').style.height =  jQuery(document).height() + 92 + 'px';
};

shop.hide_overlay_popup = function(id) {shop.hide_popup(id)};

shop.show_popup_message=function(message,title,type,width,height){switch(type){case-1:type='error';break;case 0:type='warning';break;default:type='success'}swal({html:true,title:message,type:type,text:'',showCloseButton:true})};

//confirm popup
shop.confirm = function(message, cb, cb_data) {
    swal({
        title: message.replace('\n', '<br />'),
        text: '',
        html: true,
        type: 'warning',
        showCancelButton: true,
        closeOnConfirm: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Không cám ơn!',
        confirmButtonText: 'Tôi chắc chắn!'
    }, function() {
        if (cb) {
            cb(cb_data)
        } else {
            swal('Hoàn thành!', 'Yêu cầu của bạn đã được xử lý', 'success')
        }
    })
};

//new poup theme
shop.popupSite = function(id,title,content,close,opt){close=close==undefined?'shop.hide_overlay_popup(\''+id+'\');':'';if(close!=''){close='<a href="javascript:void(0)" class="classic-popup-close" title="Đóng" onclick="'+close+'">x</a>'}var style='';if(opt){style='margin:0 auto;';if(shop.is_exists(opt.width)){style+='width:'+opt.width+'px;'}if(shop.is_exists(opt.height)){style+='height:'+opt.height+'px;'}style=' style="'+style+'"'}return shop.join('<div class="classic-popup"'+style+'>')('<div class="classic-popup-top"><div class="right"><div class="bg"></div></div></div>')('<div class="classic-popup-main">')('<div class="classic-popup-title">')('<div class="fl">'+title+'</div>'+close)('<div class="c"></div>')('</div>')('<div class="classic-popup-content">'+content+'</div>')('</div>')('<div class="classic-popup-bottom"><div class="right"><div class="bg"></div></div></div>')('</div>')()};

/*-----------------------------*/
/*        CHECKING			   */
/*-----------------------------*/

shop.is_arr = function(arr) { return (arr != null && arr.constructor == Array) };

shop.is_str = function(str) { return (str && (/string/).test(typeof str)) };

shop.is_func = function(func) { return (func != null && func.constructor == Function) };

shop.is_num = function(num) { var num = Number(num); return (num != null && !isNaN(num)) };

shop.is_int = function (x) {var y=parseInt(x);if (isNaN(y)) return false;return x==y && x.toString()==y.toString();} 

shop.is_obj = function(obj) { return (obj != null && obj instanceof Object) };

shop.is_ele = function(ele) { return (ele && ele.tagName && ele.nodeType == 1) };

shop.is_exists = function(obj) { return (obj != null && obj != undefined && obj != "undefined") };

shop.is_json = function(){};

shop.is_blank = function(str) { return (shop.util_trim(str) == "") };

shop.is_phone = function(num) {return (/^(01([0-9]{2})|09[0-9])(\d{7})$/i).test(num)};

shop.is_email = function(str) {return (/^[a-z-_0-9\.]+@[a-z-_=>0-9\.]+\.[a-z]{2,3}$/i).test(shop.util_trim(str))};

shop.is_username = function(value){ return (value.match(/^[0-9]/) == null) && (value.search(/^[0-9_a-zA-Z]*$/) > -1); }

shop.is_link = function(str){ return (/(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/).test(shop.util_trim(str)) };

shop.is_image = function(imagePath){
  var fileType = imagePath.substring(imagePath.lastIndexOf("."),imagePath.length).toLowerCase();
  return (fileType == ".gif") || (fileType == ".jpg") || (fileType == ".png") || (fileType == ".jpeg");
};

shop.is_ff  = function(){ return (/Firefox/).test(navigator.userAgent) };

shop.is_ie  = function() { return (/MSIE/).test(navigator.userAgent) };

shop.is_ie6 = function() { return (/MSIE 6/).test(navigator.userAgent) };

shop.is_ie7 = function() { return (/MSIE 7/).test(navigator.userAgent) };

shop.is_ie8 = function() { return (/MSIE 8/).test(navigator.userAgent) };

shop.is_chrome = function(){ return (/Chrome/).test(navigator.userAgent) };

shop.is_opera = function() { return (/Opera/).test(navigator.userAgent) };

shop.is_safari = function(){ return (/Safari/).test(navigator.userAgent) };

shop.isAdminUrl = function(){
  return shop.is_exists(URL_PARAMS.page) &&
		(URL_PARAMS.page == 'admin' ||
		 URL_PARAMS.page == 'edit_page' ||
		 URL_PARAMS.page == 'module' ||
		 URL_PARAMS.page == 'themes' ||
		 URL_PARAMS.page == 'admin_login' ||
		 URL_PARAMS.page == 'page');
};

/*-----------------------------*/
/*        CACHE & DATA  	   */
/*-----------------------------*/
shop.cache={get:function(key,def){if(shop.is_exists(shop._store.cache[key])){return shop._store.cache[key]}return shop.is_exists(def)?def:''},set:function(key,val){shop._store.cache[key]=val},del:function(key){shop._store.cache[key]=null;delete shop._store.cache[key]}};

/*-----------------------------*/
/*        WORKING COOKIE	   */
/*-----------------------------*/

shop.cookie = {
  mode:0, //0: default, 1: no COOKIE_ID
  set:function(name,value,expires,path,domain,secure){expires instanceof Date?expires=expires.toGMTString():typeof(expires)=='number'&&(expires=(new Date(+(new Date)+expires*1e3)).toGMTString());if(shop.cookie.mode){var r=[name+"="+escape(value)],s,i}else{var r=[COOKIE_ID+'_'+name+"="+escape(value)],s,i}if(domain==undefined&&DOMAIN_COOKIE_REG_VALUE>0){domain=DOMAIN_COOKIE_STRING}if(path==undefined){path='/'}for(i in s={expires:expires,path:path,domain:domain}){s[i]&&r.push(i+"="+s[i])}return secure&&r.push("secure"),document.cookie=r.join(";"),true},
  get:function(a){if(document.cookie.length>0){if(shop.cookie.mode==0){a=COOKIE_ID+'_'+a}c_start=document.cookie.indexOf(a+"=");if(c_start!=-1){c_start=c_start+a.length+1;c_end=document.cookie.indexOf(";",c_start);if(c_end==-1)c_end=document.cookie.length;return unescape(document.cookie.substring(c_start,c_end))}}return""}
};

/*-----------------------------*/
/*        TOOLS				   */
/*-----------------------------*/

/* function core connect */
String.prototype.E = function() {return shop.get_ele(this)}; // var obj = ('ads_zone2').E()

// join string to make theme
shop.join = function(b){var c=[b];return function extend(a){if(a!=null&&'string'==typeof a){c.push(a);return extend}return c.join('')}};

//auto increment
shop.nextNumber = (function(){var i=0;return function(){return++i}}());

shop.util_trim = function(str) {return (/string/).test(typeof str) ? str.replace(/^\s+|\s+$/g, "") : ""};

shop.util_random = function(a, b) { return Math.floor(Math.random() * (b - a + 1)) + a };

shop.get_ele = function(id) { return document.getElementById(id) };

shop.get_uuid = function() { return (new Date().getTime() + Math.random().toString().substring(2))};

shop.get_top_page = function(){if(shop.is_exists(window.pageYOffset)){return window.pageYOffset}if(shop.is_exists(document.compatMode)&&document.compatMode!='BackCompat'){return document.documentElement.scrollTop}if(shop.is_exists(document.body)){scrollPos=document.body.scrollTop}return 0};

//get all value of form
shop.get_form = function(a){var b=shop.get_ele(a);if(!shop.is_ele(b))return'';var c=[],inputs=b.getElementsByTagName("input");for(var i=0;i<inputs.length;i++){var d=inputs[i];if(d.type!='button'){c.push(d.name+"="+encodeURIComponent(d.value))}}var e=b.getElementsByTagName("select");for(var i=0;i<e.length;i++){var d=e[i],key=d.name,value=d.options[d.selectedIndex].value;c.push(key+"="+encodeURIComponent(value))}var f=b.getElementsByTagName("textarea");for(var i=0;i<f.length;i++){var d=f[i];c.push(d.name+"="+encodeURIComponent(d.value))}return c.join("&")};

/*-----------------------------*/
/*        EXTRA FUNCTIONS	   */
/*-----------------------------*/

//redirect to url
shop.redirect=function(url,is_new){if(url!=''){if(is_new){window.open(url)}else{window.location=url}}};

//reload page
shop.reload = function(){window.location.reload()};

shop.deleteCache = function(a,b){shop.ajax_popup("act=admin&code=delcache",'POST',{cacheKey:a,cacheDir:b},function(j){if(j.err==0){jQuery('#cache-con-'+j.hashKey).remove();shop.systemAlert(shop.join('<div style="font-size:14px;margin-top:5px">Xoá cache thành công</div>')(),'Hệ thống',2000)}})};

shop.showCache = function(a,c,b){shop.ajax_popup("act=admin&code=showCache",'POST',{cacheKey:a,cacheDir:b,cacheTime:c},function(j){if(j.err==0){jQuery("#showValue"+j.hashKey).html(prettyPrint(j.msg)).show()}else{shop.systemAlert(shop.join('<div style="font-size:14px;margin-top:5px">'+j.msg+'</div>')(),'Hệ thống',2000)}})};

shop.systemAlert = function(msg,title,time){var key=shop.get_uuid();time=time?time:9000;title=title?title:'Thông báo từ hệ thống';shop._active_popup(key,"","",{type:"overlay",auto_hide:time-100,overlay:{'opacity':0.3,'background-color':'#000'}});shop._active_popup('system-alert',title,shop.popupSite('system-alert',title,shop.join('<div class="content" style="padding:20px">')('<div class="box-gradien" id="site-regulations" style="padding:20px 10px;width:435px;overflow:hidden">'+msg+'</div>')('</div>')(),key),{type:'one-time',auto_hide:time,background:{'background-color':'transparent'},border:{'background-color':'transparent','padding':'0px'},title:{'display':'none'},content:{'padding':'0px','width':'500px'}});shop.hide_loading()};

//show form error
shop.raiseError=function(id,msg,focus,cl,icon){if(focus){jQuery(id).focus()}if(cl==undefined||cl==null||cl==''){jQuery(id).addClass('error')}else{jQuery(id).removeClass('error')}var p=jQuery(id).parent();jQuery('.showErr',p).remove();if(icon){jQuery('.showErrIconFalse',p).remove();jQuery('.showErrIconTrue',p).remove()}p.append((icon?'<span class="showErrIcon'+(cl?'True':'False')+'"></span>':'')+'<span class="pLeft5 showErr"><font color="'+(cl?'green':'red')+'">'+msg+'</font></span>')};

//close form error
shop.closeErr=function(id,icon){jQuery(id).removeClass('error');var p=jQuery(id).parent();jQuery('.showErr',p).remove();if(icon){jQuery('.showErrIconFalse',p).addClass('showErrIconTrue').removeClass('showErrIconFalse')}};

//set error object
shop.error={set:function(id,msg,width,cl){msg=msg?msg:'';width=width?width:430;var html=shop.join('<div class="my_msg" style="width: '+width+'px; color:red; margin: 5px auto 15px; padding:10px; background:rgb(255, 249, 215); border: 1px solid rgb(226, 200, 34); text-align: center; font-size: 15px;">')(msg)('</div>')();if(cl){jQuery('#cError',jQuery(cl)).html(html)}else{jQuery('#cError').html(html)}jQuery(id).addClass('error').focus()},close:function(id,cl){if(cl){jQuery('#cError',jQuery(cl)).html('')}else{jQuery('#cError').html('')}jQuery(id).removeClass('error')}};

//inline input
shop.showInputInline=function(obj,value,style){if(jQuery('#inline_input',obj).html()==null){obj.innerHTML=shop.join('<input type="text" value="'+value.replace(/(<([^>]+)>)/ig,"")+'" style="'+(style?style:'')+'" id="inline_input" onblur="shop.closeInputInline(this)" />')('<div class="hidden">'+obj.innerHTML+'</div>')();jQuery('#inline_input',obj).select().focus()}};
shop.closeInputInline = function(o){var p = jQuery(o).parent(), txt = jQuery('.hidden', p).html();p.html(txt)};

shop.auto_scroll = function(anchor){var target=jQuery(anchor);target=target.length&&target||jQuery('[name='+anchor.slice(1)+']');if(target.length){var targetOffset=target.offset().top;jQuery('html,body').animate({scrollTop:targetOffset},1000);return false}return true};

//input number only
shop.numberOnly = function(myfield,e){var key,keychar;if(window.event){key=window.event.keyCode}else if(e){key=e.which}else{return true}keychar=String.fromCharCode(key);if((key==null)||(key==0)||(key==8)||(key==9)||(key==13)||(key==27)){return true}else if(("0123456789").indexOf(keychar)>-1){return true}return false};

//go top button
shop.goTopStart = function(){jQuery('body').append('<a href="javascript:void(0)" onclick="jQuery(\'html,body\').animate({scrollTop: 0},1000);" class="go_top" style="display:none"></a>');jQuery(window).scroll(function(){var a=0;if(document.documentElement&&document.documentElement.clientHeight){a=document.documentElement.scrollTop}else if(document.body){a=document.body.scrollTop}if(a>0){if(shop.is_ie6()||shop.is_ie7()){a=a+jQuery(window).height()-30;jQuery('.go_top').css('top',a)}jQuery('.go_top').show()}else{jQuery('.go_top').hide()}})};

shop.enter = function(id,cb){if(cb){if(!shop.is_exists(shop._store.variable['key_listener'])){shop._store.variable['key_listener']=0}jQuery(id).keydown(function(event){if(event.keyCode==13){shop._store.variable['key_listener']=setTimeout(cb,10)}else{clearTimeout(shop._store.variable['key_listener'])}})}};

shop.numberFormat = function(number,decimals,dec_point,thousands_sep){var n=number,prec=decimals;n=!isFinite(+n)?0:+n;prec=!isFinite(+prec)?0:Math.abs(prec);var sep=(typeof thousands_sep=="undefined")?'.':thousands_sep;var dec=(typeof dec_point=="undefined")?',':dec_point;var s=(prec>0)?n.toFixed(prec):Math.round(n).toFixed(prec);var abs=Math.abs(n).toFixed(prec);var _,i;if(abs>=1000){_=abs.split(/\D/);i=_[0].length%3||3;_[0]=s.slice(0,i+(n<0))+_[0].slice(i).replace(/(\d{3})/g,sep+'$1');s=_.join(dec)}else{s=s.replace(',',dec)}return s};

shop.selectAllText = function(o){o.focus();o.select()};

//load FB sau
shop.FB = {
  conf:{fb:[]},
  loadFB: function(id, fb_html){
	shop.FB.conf.fb[shop.FB.conf.fb.length] = {id: id, html: fb_html};
  },
  pushFB: function(){
	  //load fb tu html
	  if(shop.FB.conf.fb.length > 0){
		  var html,i;
		  for(i in shop.FB.conf.fb){
			  html = '';
			  html = decodeURIComponent((shop.FB.conf.fb[i].html + '').replace(/\+/g, '%20'));;
			  jQuery('#FB_'+shop.FB.conf.fb[i].id).html(html);
		  }
	  }
  }
};

//thay doi phien ban PC & web
shop.changeMode = function (is_pc, redirect){
    var mode = is_pc ? 'pc' : 'mobile', key = 'websiteMode';
    shop.cookie.set(key, mode, 86400*365, '/');
    if(redirect && shop.is_link(redirect)){
        shop.redirect(redirect)
    }else{
        shop.reload();
    }
};

//show popup meessage or image if site close
//if title == close,will popup image and close button
shop.alertOffline={closeAlert:function(){shop.show_overlay_popup('alert-close','',shop.alertOffline.theme.closeAlert('alert-close',(WEB_STATUS_TXT!=''?WEB_STATUS_TXT:'Thông báo nghỉ')),{background:{'background-color':'transparent'},border:{'background-color':'transparent','padding':'0px'},title:{'display':'none'},content:{'padding':'0px','width':'710px','height':'410px'},esc:WEB_STATUS_TXT=='close'?true:false})},theme:{closeAlert:function(a,b){if(b=='close'){return'<div class="pop_alert" align="center"><img src="'+WEB_STATUS_IMG+'" /><a href="javascript:void(0)" onclick="shop.hide_overlay_popup(\''+a+'\')" style="color: #fff;display: block;font-size: 50px;font-weight: bold;position: absolute;right: 0;top: -40px;">x</a></div>'}else{return shop.popupSite(a,b,shop.join('<div class="pop_alert" align="center"><img src="'+WEB_STATUS_IMG+'" width="700" height="400" /></div>')(),true,{no_title:true,class_id:'site_alert'})}}}};

//show icon eye to show password as text
shop.passwordShow = {conf:{id:0,obj:null,p:null},init:function(){jQuery('.txtPass').bind('mousedown',function(){if(jQuery(this).hasClass('txtPassOn')){shop.passwordShow.conf.obj=this;shop.passwordShow.conf.id=setTimeout(function(){shop.passwordShow.showPass()},300)}}).bind('keyup',function(){shop.passwordShow.addIcon(this)}).bind('focus',function(){jQuery(this).select()});jQuery(jQuery('.txtPass').parent()).bind('mouseup',function(){shop.passwordShow.conf.p=this;clearTimeout(shop.passwordShow.conf.id);shop.passwordShow.hide()})},addIcon:function(a){var b='';if(a){b=shop.util_trim(jQuery(a).val())}if(b.length>0){if(!jQuery(a).hasClass('txtPassOn')){jQuery(a).addClass('txtPassOn')}}else{jQuery(a).removeClass('txtPassOn')}},showPass:function(){var a=jQuery(shop.passwordShow.conf.obj),attr={'val':a.val(),'cl':a.attr('class'),'id':a.attr('id'),'size':a.attr('size')};parent=a.parent();parent.prepend('<input type="text" value="'+attr.val+'" class="'+attr.cl+' txtPassOpen" id="'+attr.id+'" size="'+attr.size+'" />');a.hide()},hide:function(){jQuery('.txtPassOpen').remove();jQuery('.txtPassOn',shop.passwordShow.conf.p).show();shop.passwordShow.addIcon(jQuery('.txtPassOn',shop.passwordShow.conf.p))}};

//for debug
shop.debug=function(a){var mid=jQuery('section.content');if(mid){mid.append(prettyPrint(a))}else{jQuery('body').append(prettyPrint(a))}};
shop.viewDebug = function() {
  var mid = jQuery('section.content'),
  html,
  width = jQuery(document).width() - 50;
  if(mid.length){
	width = jQuery('section.content').width();
	html = '<div style="width:'+width+'px;margin-top:20px">' + jQuery('#debug-container').html() + '</div>';
	jQuery('#debug-container').remove();
	mid.append(html);
  }else{
	html = jQuery('#debug-container').html();
	jQuery('#debug-container').remove();
	jQuery('body').append(html);
  }
};

//auto run
shop.ready={func:{'web':[],'admin':[]},add:function(cb,admin){if(admin){shop.ready.func.admin[shop.ready.func.admin.length]=cb}else{shop.ready.func.web[shop.ready.func.web.length]=cb}},run:function(){if(shop.isAdminUrl()){if(shop.ready.func.admin.length>0){for(var i in shop.ready.func.admin){shop.ready.func.admin[i]()}}}else{if(shop.ready.func.web.length>0){for(var i in shop.ready.func.web){shop.ready.func.web[i]()}}}}};

//redefine window.alert function
shop.redefineAlert = function(){
  (function(proxied) {
	window.alert = function() {
	  var message = "" + ((!arguments[0]) ? 'null': arguments[0]),
	  cb = (!arguments[1]) ? '' : arguments[1];
	  swal({title: message.replace('\n', '<br />'), type: 'info', text: '', html: true, showCloseButton: true}, function(){
		if(cb && cb != ''){
		  cb();
		}
	  });
	  //return proxied.apply(this, arguments); //goi ham cu cua window.alert
	};
  })(window.alert);
};

//add default job to run after load done
shop.ready.add(function(){
  shop.redefineAlert();
  //shop.passwordShow.init();
  //shop.goTopStart();
}, true);

shop.ready.add(function(){
  //shop.redefineAlert();
  if(WEB_STATUS == 'close'){
	shop.alertOffline.closeAlert();
  }
});