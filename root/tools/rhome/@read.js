
document.write("<script src='../root/skin/jslib/jsbase.js'></script>");
document.write("<script src='../root/skin/a_jscss/mkdown.js'></script>");

function _readInit(){
	rdfStyle(); 
	rdfMDown();
	rdfDShow();
	//try{}catch(ex){}
}
var doc_url = '../code/tpls/dev/d_start/doc_down.txt';
window.onload = function (){
	setTimeout('_readInit()',100);
} 

function rdfDShow(){
    url = doc_url+'?_r='+(new Date).getTime(); 
	var http = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Msxml2.XMLHttp"); 
    http.open("get", url, true);  
    http.onreadystatechange = function(){
		if(http.readyState==4){ if(http.status==200){ 
			var data = mkDown(http.responseText,1); 
			data = '<hr><pre>['+doc_url+']'+data+'</pre>'; 
			jsElm.jeTag('body').innerHTML += data;
		} }    
	} 
	http.send(null); 
}
function rdfMDown(){
	var obody = jsElm.jeTag('body'); 
	var data = obody.innerHTML; 
	data = mkDown(data,1); 
	data = mkDLink(data, doc_url);
	obody.innerHTML = data;
}

function rdfStyle(){
	var text = 'html{line-height:120%;}';
	var style = document.createElement("style"); 
	jsElm.jeTag('head').appendChild(style);
	if(style.styleSheet){ //for ie
		style.styleSheet.cssText = text;
	}else{ //for w3c
		style.appendChild(document.createTextNode(text)); 
	}
}

