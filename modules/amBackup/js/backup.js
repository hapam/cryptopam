shop.backup = {
	del: function(id, time){
		if(id > 0){
			shop.confirm('Bạn sẽ xóa backup ngày '+time+' ?',function(){
				shop.ajax_popup('act=backup&code=del','POST',{id:id},
					function(j){
						alert(j.msg, shop.reload);
				});
			});
		}
	},
	restore: function(id, time){
		if(id > 0){
			shop.confirm('Bạn sẽ khôi phục lại thời điểm ngày '+time+' ?',function(){
				shop.ajax_popup('act=backup&code=restore','POST',{id:id},
					function(j){
						alert(j.msg);
				});
			});
		}
	},
	add: function(){
		shop.confirm('Bạn muốn tạo bản backup?\nQuá trình có thể diễn ra trong vài phút', function(){
			shop.ajax_popup('act=backup&code=add','POST',{},
				function(j){
					alert(j.msg, shop.reload);
			});
		});
	}
};