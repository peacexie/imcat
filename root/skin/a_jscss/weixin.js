//微信相关js

function wxMenuClear(imuid){
	$(jsElm.jeID('fm['+imuid+'][name]')).val('');
	$(jsElm.jeID('fm['+imuid+'][val]')).val('');
}

function wxSetDebugType(key,init){
	var kch3 = key.substr(0,3); //console.log(kch3);
	$('.tbdata tr').each(function(index, element) {
        var html = $(this).find('td:first').html(); //console.log(element);
		if(html.indexOf('--,')>0){ 
			 $(this).hide();
		}
		if(html.indexOf(','+kch3+',')>0){ 
			 $(this).show();
		}
    });
	if(init){
		$(':radio[name="deubg[type]"]').last().attr("checked",true);
	}
}
function wxGetUserPage(){
	var pstart = (wu_page-1)*50;
	var pend = pstart+50; if(pend>wu_count) pend=wu_count;
	var arr = wu_list.split(',');
	var ids = '';
	for(var i=pstart;i<pend;i++){
		ids += ','+arr[i];
	}
	var url = 'actys=getUinfo&ustr='+ids+'&kid='+wu_kid+'';
	$('#tip_errors').hide();
	$.getScript(wu_urlbase+url, function(){ 
		if(data.errcode){
			$('#tip_errors').show();
			$('#tip_errors').html('Error:['+data.errcode+']<br>'+data.errmsg);
		}else if(data){ 
			wxSetUserPage(data);
		}  
	});
}
function wxSetUserPage(data){
	data = data.user_info_list;
	$('[id^=wu_row]').hide();
	for(var i=0;i<data.length;i++){ 
		var itm = data[i]; //jsLog(itm);
		var cheadimg = "<a href='"+itm.headimgurl+"' target='_blank'>头像</a>";
		var csex = itm.sex==1 ? '男' : '女';
		var atime = wxFmtLocalTime(itm.subscribe_time);
		var j = i+1, row = jsElm.jeID('wu_row'+j);
		$(row).show();
		$(row).find('td').eq(0).html(itm.nickname+(itm.subscribe?'':'(?)'));
		$(row).find('td').eq(1).html(itm.openid);
		$(row).find('td').eq(2).html(itm.groupid);
		$(row).find('td').eq(3).html(itm.city);
		$(row).find('td').eq(4).html(cheadimg);
		$(row).find('td').eq(5).html(csex);
		$(row).find('td').eq(6).html(atime); //onclick=\"return winOpen(this,'群发信息',780,560);\"
		$(row).find('td').eq(7).html("<a href='"+wu_msgurl+itm.openid+"' onclick=\"return winOpen(this,'发信息')\">发信息</a>");
	}
}
function wxFmtLocalTime(stamp) {     
	return new Date(parseInt(stamp) * 1000).toLocaleString();//.substr(0,17);
} 
function wxGetPageBar(pnow) {
	var pall = Math.ceil((wu_count)/50);
	var pmin = pnow-5; if(pmin<1) pmin=1;
	var pmax = pnow+5; if(pmax>pall) pmax=pall;
	var css = '', i=0; //console.log(wu_count + ':'+ pall);
	var sbar = "";
	if(pmin>1){
		i=1; css = i==pnow ? 'pg_num cF0F' : 'pg_num';
		sbar += "<li class='"+css+"'><a style='cursor:pointer' onclick='wxGetPageAct("+i+")'>"+i+"...</a></li>";	
	}
	for(i=pmin;i<=pmax;i++){
		css = i==pnow ? 'pg_num cF0F' : 'pg_num';
		sbar += "<li class='"+css+"'><a style='cursor:pointer' onclick='wxGetPageAct("+i+")'>"+i+"</a></li>";	
	}
	if(pmax<pall){
		i=pall; css = i==pnow ? 'pg_num cF0F' : 'pg_num';
		sbar += "<li class='"+css+"'><a style='cursor:pointer' onclick='wxGetPageAct("+i+")'>..."+i+"</a></li>";
	}
	sbar += "<li class='pg_total'>"+wu_count+"</li><li class='pg_pagno'>"+pnow+"/"+pall+"</li>";
	$('.pg_bar').html(sbar);
	/*
    <li class='pg_first'>&laquo;||首页</li>
    <li class='pg_prev'>&lt;|上页</li>
    <li class='pg_now'>1</li>
    <li class='pg_next'>下页|&gt;</li>
    <li class='pg_last'>尾页||&raquo;</li>
    <li class='pg_last'><a href='/08tools/yssina/1/root/run/adm.php?file=dops/a&mod=news&prec=22&page=2&ptype=end' >尾页</a></li>
    <li class='pg_total'>2</li>
    <li class='pg_pagno'>1/1</li>
	*/
}
function wxGetPageAct(page) {
	wu_page = page;
	wxGetUserPage(); 
	wxGetPageBar(page);
}

function wxKwdsetSence(istext){ 
	var kobj = jsElm.jeID('fm[keyword]'); 
	kobj.value = istext ? '' : 'follow_autoreply_info'; 
	kobj.readonly = istext ? false : true; 
}

function wxSendType(type){ 
	return;
}

// -------------------------------- 

