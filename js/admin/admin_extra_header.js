shop.admin.tool = {
	time: 0,
	clock_id: 0,
	start:function(obj){
		if(obj.time){
			shop.admin.tool.time = parseInt(obj.time);
			shop.admin.tool.clock(true);
		}
	},
	clock:function(start){
		var id = 'clock-timer', today, str = '', arr = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'], h, m, s, d, mon;
		//raise time
		shop.admin.tool.time++;

		//display timer
		today = new Date(shop.admin.tool.time * 1000);
		h = today.getHours();
		m = today.getMinutes();
		s = today.getSeconds();
		d = today.getDate();
		mon = today.getMonth() + 1;
		str = shop.join
			(arr[today.getDay()]+', ngày '+(d>9?d:'0'+d)+'/'+(mon>9?mon:'0'+mon)+'/'+today.getFullYear())
		('<span>')
			((h>9?h:'0'+h)+":"+(m>9?m:'0'+m)+':'+(s>9?s:'0'+s))
		('</span>')();
		jQuery('#'+id).html(str);
		
		//run & run
		shop.admin.tool.clock_id = setTimeout(shop.admin.tool.clock,1000);
	}
};

shop.admin.leftMenu = {
	init:function(){
		jQuery('ul.nav li a').hover(
			function(){
				jQuery('ul.nav li a.active').removeClass('active');
				jQuery('.showing_menu').hide().removeClass('showing_menu');
				if(jQuery(this).hasClass('closeMenu')){
					jQuery('#subNav'+jQuery('.nowShowing').attr('id')).show().addClass('showing_menu');
				}else{
					jQuery('#subNav'+this.id).show().addClass('showing_menu');
				}
			},
			function(){
				if(!jQuery(this).hasClass('closeMenu')){
					jQuery(this).addClass('active');
				}else{
					jQuery('.nowShowing').addClass('active');
				}
			}
		);
	}
};

var shop_menu_ctrl = 1;
shop.menu_ctrl = function(){
	if(shop_menu_ctrl == 1){
		shop_menu_ctrl = 0;
		jQuery('.secNav').hide();
		jQuery('#sidebar').css({width:"100px"});
		jQuery('#content').css({margin:"0 0 0 101px",zIndex:"1000"});
	}else{
		shop_menu_ctrl = 1;
		jQuery('.secNav').show();
		jQuery('#sidebar').css({width:"auto"});
		jQuery('#content').css({margin:"0 0 0 327px"});
	}
}

shop.ready.add(shop.admin.leftMenu.init, true);