
// 替换标题或加粗isb=1,替换链接,适合<pre>里显示文本
function mkDown(data,isb){
	var darr = data.split("\n"), tmpb = '', tmpi = '';
	for(var i=0;i<darr.length;i++){
		if(darr[i].length<5){	
			//if(nuls>0) continue;
		}else{
			tmpb = tmpi = darr[i].replace(/\r/g,'');
			tmpi = mkDTitle(tmpi,isb);
			tmpi = mkDLink(tmpi);
			data = data.replace(tmpb,tmpi);
		}
	}
	return data;  
}

// ###-titles
function mkDTitle(str,isb){ 
	for(var i=7;i>0;i--){
		var fix0 = str.substring(0,i);
		fix1 = fix0.replace(/\#/g,'');
		if(fix0 && fix1==''){
			var tag = isb ? 'b' : 'h'+i;
			str = '<'+tag+'>'+str+'</'+tag+'>';	
		}
	}
	return str;
}

// a-link
function mkDLink(str, url){
	if(url){
		str = str.replace(url,"<a href='"+url+"' target='_blank'>"+url+"</a>");
	}else{
		reg = /(http:\/\/[\S]+)(?![^<]+>)/gi; 
		str = str.replace(reg,"<a href='$1' target='_blank'>$1</a>");
	}
	return str;
}
