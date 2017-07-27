shop.myCoin = {
	test: function(){
		shop.ajax_popup('act=myCoin&code=test','POST',{number:10},
			function(j){
				if(j.err == 0){ // success
					alert(j.msg+' '+j.say);
				}else{
					alert(j.msg);
				}
		});
	}
};
shop.myCoin.test();