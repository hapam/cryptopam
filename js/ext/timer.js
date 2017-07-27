//object timer for play multi time
//shop.timerObject.start(12,'timeTitleSmall12',time_end,1,2,false,time_start);
shop.timerObject = {
  obj: {},
  counter:0,
  now:TIME_NOW,
  clock_id: 0,
  go:function(){
	if(shop.timerObject.counter > 0){
	  shop.timerObject.countTime();
	}
  },
  start: function(id, container, time, is_day, type, run_class, start){
	shop.timerObject.obj[container] = {id: id, c: container, time: time, isDay:is_day?1:0, type:type?type:0, cl:run_class?1:0, start:start?start:0};
	shop.timerObject.counter++;
  },
  countTime:function(){
	shop.timerObject.now ++;
	for(var i in shop.timerObject.obj){
	  shop.timerObject.displayTime(shop.timerObject.obj[i].c);
	}
	shop.timerObject.clock_id = setTimeout(function(){shop.timerObject.countTime()},1000);
  },
  displayTime: function(id){
	if(shop.timerObject.obj[id].start > 0 && shop.timerObject.obj[id].start > shop.timerObject.now){
	  //ko lam gi ca
	}else{
	  var time = shop.timerObject.obj[id].time - shop.timerObject.now, type = shop.timerObject.obj[id].type,
	  hour_title = '',
	  min_title = '',
	  sec_title = '';
	  if(time > 0){
		var day = 0, hour = 0, min = 0, sec = 0;
		if(shop.timerObject.obj[id].isDay){
		  day = Math.floor(time/86400);
		  time = time%86400;
		  if(day > 0){
			day = day + ' ngày, ';
		  }else{
			day = '';
		  }
		}else{
		  day = '';
		}
		switch(type){
		  case 1: hour_title = ' giờ ';  min_title = ' phút ';  sec_title = ' giây';	break;
		  case 2: hour_title = 'h ';  min_title = "' ";  sec_title = 's';				break;
		  case 3: hour_title = 'h: ';  min_title = "p: ";  sec_title = 's';			break;
		  default:hour_title = 'h : ';  min_title = "p : ";  sec_title = '&quot;';	break;
		}
		hour = Math.floor(time/(60*60));
		min = Math.floor((time%(60*60))/60);
		sec = Math.floor(time - hour*60*60 - min*60);
		time = day;
		time+= (hour>9?'':'0')+(hour>0?hour:0)+hour_title;
		time+= (min>9?'':'0')+(min>0?min:0)+min_title;
		time+= ((sec>9&&sec<60)?'':'0')+((sec>0&&sec<60)?sec:0)+sec_title;
		if(shop.timerObject.obj[id].cl == 1){
		  jQuery('.'+shop.timerObject.obj[id].c).html(time);
		}else{
		  jQuery('#'+shop.timerObject.obj[id].c).html(time);
		}
		return true;
	  }
	}
	return false;
  }
};