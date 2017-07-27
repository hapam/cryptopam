shop.admin.exporter = {
	order:function(){
		var data = {
			fullname: jQuery('#user_name').val(),
			email: jQuery('#email').val(),
			phone: jQuery('#mobi_phone').val(),
            item_id: jQuery('#item_id').val(),
            status: jQuery('#status').val(),
			from: jQuery('#created_time').val(),
			to: jQuery('#created_time_to').val(),
            type: jQuery('#type').val()
		};
		window.location = BASE_URL + 'export.html?cmd=order' + shop.admin.system.fetchDataToUrl(data);
	}
};
