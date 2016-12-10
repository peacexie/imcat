
var wNames = new Array("Sun", "Mon", "Tue", "Wen","Thu", "Fri", "Sat");
// 执行一个任务
function jobRun(hm,i){
	//jsLog('do:'+jid);
	var jid = pLists[i][0];
	if(!jsElm.jeID('job_'+jid)){
		//var url = _cbase.run.roots + jcronRun(0,0,1) + '&fjob=' + jid;
		var url = _cbase.run.roots + '/plus/ajax/cron.php?fjob='+jid+'&'+urlp+'&'+jsRnd();
		var title = jid + ' @ <b>' + hm + '</b><input class="url" value="'+url+'">';
		var str = '<li id="job_'+jid+'">'+title+'<iframe src="'+url+'"" width="100%" height="40"></iframe></li>';
    	$('#job_lists').append(str);
    }
}
// 整理任务列表
function jobReset(hm,s){
	if(s && s%5==0){ 
		$('#job_lists li').each(function(i,e){
			var t = $(this).find('b').html(); 
			if(t!=hm) $(this).remove();
		});
	}
	num = $('#job_lists li').length;
	idSate.innerHTML = num ? lang('jcore.rplan_nrun',num) : lang('jcore.rplan_nrun',rpaln_null); 
}
// 获取现在时间
function timeNow(){
	var oTime = new Date();
	var hh = oTime.getHours(); if(hh<=9) hh="0"+hh;
	var mm = oTime.getMinutes(); if(hh<=9) mm="0"+mm;
	var ss = s = oTime.getSeconds(); if(ss<=9) ss="0"+ss;
	var y = oTime.getFullYear();
	var m = oTime.getMonth(); m++;
	var d = oTime.getDate();
	var w = oTime.getDay();
	idNow.innerHTML = y+'-'+m+'-'+d+' '+hh+':'+mm+':'+ss+'('+wNames[w]+')';
	return {'hm':hh+':'+mm, 'w':w, 's':s};
}
// 定时器
function timeSec(){
	var now = timeNow();
	for(var i=0;i<pLists.length;i++){
		var t = pLists[i][1].split('@');
		if(t[0]==now.hm){
			if(t[1] && parseInt(t[1])!=now.w) continue;
			jobRun(now.hm,i);
		}
	}
	jobReset(now.hm,now.s);
	setTimeout("timeSec()",1000); 
}

