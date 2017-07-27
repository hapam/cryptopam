shop.follow = {
	data:{},
	run:function(){
		setTimeout(shop.follow.loadPoloData, timeLoad*1000);
	},
	loadPoloData:function(){
		shop.ajax_popup('act=follow&code=load', "GET", {},function (j) {
			if (j.err == 0) {
				shop.follow.drawData(j.data);
				shop.follow.run();
			}
		});
	},
	drawData:function(d){
		if(d){
			for(var i in d){
				jQuery('#'+i).html(shop.follow.drawTable(d[i].pairs));
			}
		}
	},
	drawTable:function(d){
		var html = '',
		rate = 0,
		money = 0,
		totalMoney = 0,
		totalSpent = 0,
		def = shop.follow.data;
		if(d){
			html = shop.join
			('<table class="table table-bordered">')
				('<thead>')
					('<tr>')
						('<th>Coin</th>')
						('<th>Purchased Price</th>')
						('<th>Price</th>')
						('<th>% L/W</th>')
						('<th>Money L/W</th>')
						('<th>Inv</th>')
						('<th>Num</th>')
					('</tr>')
				('</thead>')
				('<tbody>')();
				for(var i in d){
					if (def[d[i].id]){
						rate = shop.follow.calRate(def[d[i].id].price, d[i].last);
						money= shop.follow.calLoss(def[d[i].id].price, d[i].last, def[d[i].id].quantity);
						totalMoney += money;
						totalSpent += parseFloat(def[d[i].id].quantity);
						
						html += shop.join
						('<tr>')
							('<td><b>'+d[i].name+'</b></td>')
							('<td>'+def[d[i].id].price+'</td>')
							('<td>'+d[i].last+'</td>')
							('<td class="'+(rate > 0 ? 'success' : 'danger')+'" align="right">'+shop.numberFormat(rate, 2)+'</td>')
							('<td>'+(def[d[i].id].quantity > 0 ? (money > 0 ? '+' : '')+shop.numberFormat(money, 4) : '')+'</td>')
							('<td>'+(def[d[i].id].quantity > 0 ? shop.numberFormat(def[d[i].id].quantity,4) : '')+'</td>')
							('<td>'+(def[d[i].id].quantity > 0 ? shop.numberFormat(def[d[i].id].quantity/def[d[i].id].price,4) : '')+'</td>')
						('</tr>')();
					}
				}
				html += shop.join
						('<tr>')
							('<td colspan="4" align="right"><b>Total</b></td>')
							('<td>'+shop.numberFormat(totalMoney, 4)+'</td>')
							('<td>'+shop.numberFormat(totalSpent,4)+'</td>')
							('<td></td>')
						('</tr>')();
			html += shop.join
				('</tbody>')
			('</table>')();
		}
		return html;
	},
	calRate:function(buy, now){
		var rate = Math.abs((buy - now)*100/buy);
		return buy > now ? -rate : rate;
	},
	calLoss:function(buy, now, spent){
		var rate = shop.follow.calRate(buy, now);
		return spent*rate/100;
	}
};

shop.ready.add(shop.follow.run, false);