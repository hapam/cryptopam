shop.error.newStyle = {
	set:function(obj, msg){
		shop.error.newStyle.close();
		var p = jQuery(obj).parent();
		p.append(shop.join('<div class="alert alert-danger alert-dismissible mTop5" id="myAlert">')
			('<a href="javascript:void(0)" class="close">&times;</a>'+msg)
		('</div>')());
		jQuery(obj).focus().select();
	
		jQuery(document).ready(function(){
			jQuery(".close").click(function(){
				shop.error.newStyle.close();
			});
		});
	},
	close:function(id){
		jQuery("#myAlert").alert("close");
	}
};

shop.exchange = function(){
	var price = parseFloat(jQuery('#exchange').val()),
	per = parseInt(jQuery('#percent').val());
	
	price = price + parseFloat(price*per/100)
	
	jQuery('#result-exchange').removeClass('hide');
	jQuery('#result-exchange div').html("Result: "+shop.numberFormat(price, 8));
}