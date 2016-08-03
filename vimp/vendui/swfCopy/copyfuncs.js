
// reset copy button
function copyReset(dataID,swfID,cfgs){
	var data,key; data = dataID; //isVal,noEnc
	if(!cfgs.isVal){
		key = dataID.replace(/\W/g, "");
		var obj = document.getElementById(key);
		if(obj && obj.innerHTML) data = obj.innerHTML;
		if(obj && obj.value) data = obj.value;
	} //console.log(data);
	data = cfgs.noEnc ? data : encodeURIComponent(data);
	icon = cfgs.icon ? cfgs.icon :  _cbase.path.vendui+'/swfCopy/swfbtn.png';
	var flashvars = { content:data, uri:icon };
	var params = {
		wmode: "transparent",
		allowScriptAccess: "always"
	};
	swfobject.embedSWF(_cbase.path.vendui+'/swfCopy/clipboard.swf', swfID, "52", "25", "9.0.0", null, flashvars, params);
	//var so = new SWFObject(swf, id, width, height, version, background-color [, quality, xiRedirectUrl, redirectUrl, detectKey]);
}

// loadJs,runCode
function loadRun(url,runCode){     
	url = _cbase.path.vendui+url; //console.log(url);
	var head = document.getElementsByTagName("head")[0];
	var script = document.createElement('script');
	script.onload = script.onreadystatechange = script.onerror = function (){
		if (script && script.readyState && /^(?!(?:loaded|complete)$)/.test(script.readyState)) return;
		script.onload = script.onreadystatechange = script.onerror = null;
		script.src = '';
		script.parentNode.removeChild(script);
		script = null;
		if(runCode){ 
			eval(""+runCode+";");
		}
	}
	script.charset = "utf-8";
	script.src = url;
	try { head.appendChild(script); } 
	catch (exp) {}
}

// flash callback
/* //time,旧版本单位为秒,新版本单位为毫秒
function copySuccess(){
	layer.tips('复制成功！', '#copySwfID', { tips: [1, '#134d9d'], time: 1});
}
var copySwf_cbackID = 1;
function bak_copySuccess(){ 
	//如果有多个copy, 设置外部变量var copySwf_cbackID=1;, 使显示提示信息不跟随最后一个button
	if(typeof(copySwf_cbackID)=='undefined'){
		var showid = 'copySwfID_$id';
		layer.tips('复制成功！', '#'+showid, {style:['background-color:#134d9d;color:#FFF;','#134d9d'], time:1}); 
	}else{
		layer.msg('复制成功！',1);
	}
}
*/
