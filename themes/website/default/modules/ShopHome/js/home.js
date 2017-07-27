shop.home = {
	run:function(){
		setTimeout(shop.home.loadPoloData, 5*1000);
	},
	loadPoloData:function(){
		shop.ajax_popup('act=home&code=load', "GET", {},function (j) {
			if (j.err == 0) {
				shop.home.drawData(j.data);
				shop.home.run();
			}
		});
	},
	drawData:function(d){
		if(d){
			for(var i in d){
				jQuery('#'+i).html(shop.home.drawTable(d[i].pairs));
			}
		}
	},
	drawTable:function(d){
		var html = '';
		if(d){
			html = shop.join
			('<table class="table table-bordered">')
				('<thead>')
					('<tr>')
						('<th>Coin</th>')
						('<th>Price</th>')
						('<th>Change</th>')
					('</tr>')
				('</thead>')
				('<tbody>')();
				for(var i in d){
					html += shop.join
					('<tr>')
						('<td><b>'+d[i].name+'</b></td>')
						('<td>'+d[i].last+'</td>')
						('<td class="'+(d[i].percentChange > 0 ? 'success' : 'danger')+'">'+d[i].percentChange+'</td>')
					('</tr>')();
				}
			html += shop.join
				('</tbody>')
			('</table>')();
		}
		return html;
	}
};

shop.ready.add(shop.home.run, false);