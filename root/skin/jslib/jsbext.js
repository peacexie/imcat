
//---------------------------------------------------
// *** 基本扩展函数
//--------------------------------------------------- \

function setCookie(name,value,exp_secs,path,domain){
	var key = _cbase.ck.ckpre+name;
	var never = new Date(); // _cbase.sys.tzone*3600时区...
	never.setTime(never.getTime()+exp_secs*1000); //单位毫秒  
	var sexp = exp_secs ? "; expires="+ never.toGMTString()+";" : ''; 
	path = (path != null && path != '') ? '; path=' + path : '; path='+_cbase.ck.ckpath;
	domain = (domain != null && domain != '') ? '; domain=' + domain : '';
	document.cookie = key+"="+escape(value)+sexp+path+domain;	
}

function getCookie(name){
	var key = _cbase.ck.ckpre+name;
	if (document.cookie.length>0){
		c_start=document.cookie.indexOf(key + "=")
		if (c_start!=-1){ 
			c_start=c_start + key.length+1 
			c_end=document.cookie.indexOf(";",c_start)
			if (c_end==-1) c_end=document.cookie.length
			return unescape(document.cookie.substring(c_start,c_end))
		} 
	}
	return '';
}

// 系统开窗(System)
function winOpen(e,title,width,height,ext){
	var width = width ? width : 640;
	var height = height ? height : 480;
	var url,title,wt=_cbase.sys_open;
	if(isObj(e,'s')){
		url = e;
		title = title ? title : "winOpen";
	}else{ //a
		url = e.href;
		title = title ? title : e.innerHTML;	
	}
	url += (url.indexOf('?')>0 ? '' : '?') + '&' + jsRnd('dialog');
	if(wt==1){ 
		var _x = (screen.width-width)/2, _y = (screen.height-height)/2, id = ext ? '_win_'+jsKey(ext) : '_win_';
		var p = ",left="+_x+",top="+_y+",width="+width+",height="+height+"";
		window.open(url,id,'scrollbars=yes,toolbar=no,location=no,status=no,menubar=no,resizable=yes'+p); 
	}else if(wt==2){ 
		$.webox({title:title, iframe:url, width:width, height:height, bgvisibel:false }); 
	}else if(wt==3){  
		tipsWindown(title,'iframe:'+url,width,height,'true','','0','content-boxCss');
	}else if(wt==4){
		var ops = {type:2, fix:false, maxmin:true, title:[title,true], area:[width+'px',height+'px'], content:[url]};
		layer.open(ops);
	}
	return false; 
}
// 窗口大小-winSize() --- quirksmode.org
function winSize(re){ 
	if(!re) re = 'win';
	if(re=='win'){ //浏览器时下窗口可视区域宽/高度
		w = $(window).width();
		h = $(window).height();
	}else if(re=='doc'){ //浏览器时下窗口文档的宽/高度
		w = $(document).width();
		h = $(document).height(); 
	}else if(re=='body'){ //浏览器时下窗口文档body的宽/高度
		w = $(document.body).width();
		h = $(document.body).height();
	}else if(re=='out'){ //浏览器时下窗口文档body的总宽/高度 包括border padding margin
		w = $(document.body).outerWidth();
		h = $(document.body).outerHeight();	
	}else if(re=='scroll'){ //获取滚动条到左边/顶部的垂直宽/高度
		w = $(document).scrollLeft();
		h = $(document).scrollTop();	
	} //jsLog(h);
	return {w:w,h:h};
}
// 
function winAutoMargin(div,gap){
	if(!div) div = 'topMargin';
	if(!gap) gap = 20;
	wv = winSize('win'); //jsLog(wv);
	wc = winSize('out'); //jsLog(wc);
	if(wv.h>wc.h+gap){ 
		$('#'+div).show();
		$('#'+div).height((wv.h-wc.h-gap/2)*0.33);
	}else{
		$('#'+div).hide();	
	}
}

// 前后台 表单 ===================================================================================================

function fsCHidd(fmid){
	jsElm.jeID(fmid+'_vBox').style.display = 'none';
}
// 认证码初始化(认证码:onFocus触发)
function fsCode(fmid,reLoad,x,y){
	var box = jsElm.jeID(fmid+'_vBox');
	if(box.innerHTML.length<24){
	  x = x ? x : 3; y = y ? y-25 : -20;
	  var img = '<samp class="fs_vimg_span" style="left:'+x+'px;top:'+y+'px;">';
	  img += '<img id="'+fmid+'_vimg" src="'+_cbase.run.roots+'/skin/a_img/blank.gif" onclick=\'fsCode("'+fmid+'","reLoad")\' title="点击刷新/换一组" />';
	  img += '<samp class="fs_vimg_close" onclick=\'fsCHidd("'+fmid+'")\' title="隐藏">[X]</samp></samp>';
	  box.innerHTML = img; 
	  reLoad = 1;
	} 
	if(box.style.display=='none') box.style.display = ''; 
	//超时检测...
	var fimg = jsElm.jeID(fmid+'_vimg');
	if(reLoad){ 
		var fimg = jsElm.jeID(fmid+'_vimg');
		var para = "?mod="+fmid+"&"+jsRnd()+"&"+_cbase.safil.url;
		fimg.setAttribute("src",_cbase.run.roots+"/plus/ajax/vimg.php"+para);
	}
	$("p#evf_vtip").remove(); //onFocus触发后,保证把前一个tip清理掉,否则挡住了认证码.
}
// 重新加载-安全配置项 ??? 
function fsReLoad(fmid){
	var js = jsElm.jeID('jssrc_'+fmid);
	js.src += 1;
}
// 表单认证码
function fsInit(fmid,css1,css2,tabi){
	url = _cbase.run.roots+'/plus/ajax/cajax.php?act=fsInit&fmid='+fmid;
	if(css1) url += '&css1='+css1;
	if(css2) url += '&css2='+css2;
	if(tabi) url += '&tabi='+tabi;
	para = '&'+_cbase.safil.url+'&'+jsRnd();
	document.write('<script src="'+url+para+'" id="jssrc_'+fmid+'"></'+'script>');
}

// 选择-所有
function fmSelAll(e,fm,r,no) {
	if(!fm) fm = 'fmlist';
	if(!r) r = 'tr';
	if(!no) no = 0;
	var row = jsElm.jeID(fm).getElementsByTagName(r);
	for(var i=0;i<row.length;i++){
		var ir = row[i].getElementsByTagName('input');
		if(ir.length>0) ir[0].checked = e.checked;;
	}
}
// 选择-组, class以cbg_开头,如:class="cbg_types" 
function fmSelGroup(e,part) {
	$("input.cbg_"+part).each(function() {
		$(this).prop("checked", e.checked); 
	});	
}

// {lang(core.view_times,$click)}
// {lang(core.sys_name)}
function lang(mk, val){
	/*
	$arr = explode('.',$mk); 
	$re = self::get($arr[1], $arr[0]);
	if(strlen($val)>0){
		$re = str_replace('{val}',$val,$re);
	} 
	return $re;
	*/
}

// 前台ajax调用资料使用 ===================================================================================================

function jcronRun(tpldir,mkv,reurl){
	tpldir = tpldir ? tpldir : ((typeof(_cbase.run.tpldir)=='undefined') ? '' : _cbase.run.tpldir);
	mkv = mkv ? mkv : ((typeof(_cbase.run.mkv)=='undefined') ? '' : _cbase.run.mkv);
	mkv = (typeof(_cbase.run.mkv)=='undefined') ? '' : _cbase.run.mkv;
	var url = '/plus/ajax/cron.php?tpldir='+tpldir+'&mkv='+mkv+'&'+_cbase.safil.url+'&'+jsRnd();
	if(reurl) return url;
	jsImp(url); 
}
function jtagSend(){
	var base = window.location.pathname; //'http://'+window.location.host+
	var url = '/plus/ajax/jshow.php?tpldir='+_cbase.run.tpldir+'&mkv='+_cbase.run.mkv+'&'+jsRnd(); 
	var n = 0, last = 0;
	$("[id^='jsid_']").each(function(){
		var id = this.id; 
		var val = jtagPara(this);
		var data = '&'+id+'='+val; 
		if(id.length>12){ // && val.length>3
			lena = url.length+data.length+base.length;
			if(lena>2000){ // 2038
				jtagRep(id,'<i class="cC3C">js标签太多,{'+id+'}无法显示。('+lena+')</i><br>');
			}else{
				url += data; last = data.length;
			} 
		}
	}); 
	if(url.indexOf('&jsid')>0){ 
		jsImp(url); //console.log((url.length)+':'+(last)+':'+url); 
	}
}
function jtagPara(e){
	var s = $(e).html().replace('0<!--','').replace('<!--','').replace('-->',''); 	
	s = urlEncode(s,1,1);
	return s;
}
function jtagRep(id,rep){
	$('#'+id).after(rep);
	$('#'+id).remove();
}

// 二维码例子(jq) =========================================================================================

function qrCode(id,info,w,h){ //id='qrcodeA1', info='www.txmao.com'
    jQuery('#'+id).qrcode({render:"table",text:info,width:90,height:90,correctLevel:3 });
}

// 拼音/简繁 ===================================================================================================
 
function pinyinMain(str){
  var pyTab = pycfgTab();
  py = ""; //(nou)耨,1;
  for(var i=0;i<str.length;i++){
	  ch = str.charAt(i);
	  code = str.charCodeAt(i);
	  if(code<128){
	  	  py += str.charAt(i); 
	  }else{
		  p = pyTab.indexOf(ch);
		  if(p>0){
			  s = pyTab.substr(0,p);
			  p = s.lastIndexOf("(");
			  s = s.substr(p+1);
			  s = s.substr(0,s.indexOf(')'));
			  py += s; 
		  }
	  }
  }
  return py; 
}

function jianfanMain(str,type){
  var jTab = jfcfgJian();
  var fTab = jfcfgFan();
  if(type=='j2f'){ tab1 = jTab; tab2 = fTab; }
  if(type=='f2j'){ tab2 = jTab; tab1 = fTab; }
  jf = ""; //(nou)耨,1;
  for(var i=0;i<str.length;i++){
	  ch = str.charAt(i);
	  code = str.charCodeAt(i);
	  if(code<128){
	  	  jf += ch; 
	  }else{
		  p1 = tab1.indexOf(ch);
		  if(p1>0){
			  ch = tab2.charAt(p1);
			  jf += ch; 
		  }else{
			  jf += ch; 
		  }
	  }
  }
  return jf; 
}

// 联动select ===================================================================

// 联动select-初始化
// mselInit('typlays','fmxid','china','c0769','省份,地区');
function mselInit(vsid,fmid,dkey,vdef,titles){
	var vlay,a,i,pid,ops,s,j;
	eval("window._msel_"+fmid+"_titles = '"+(titles ? titles : '')+"';");
	vlay = vdef ? dataLays(dkey,vdef) : ';';
	a = vlay.split(';'); 
	s = '<input name="'+fmid+'" id="'+fmid+'" type="hidden" value="'+vdef+'">';
	for(i=0;i<a.length-1;i++){ 
		pid = i ? a[i-1] : '0';
		ops = mselOpts(dkey+'-'+pid,a[i]); 
		s += mselElms(fmid,dkey,i,ops);
		j = i;
	}
	if(!(vlay==';')){
		ops = mselOpts(dkey+'-'+a[j]); 
		if(ops) s += mselElms(fmid,dkey,a.length-1,ops);
	}
	$('#'+vsid).html(s);
}
// 联动select-选择事件
// onchange=mselAct(fmid,dkey,no)
function mselAct(fmid,dkey,no){
	id = fmid+"_"+no;
	val = $('#'+id).val(); 
	$(jsElm.jeID(fmid)).val(val); 
	if(val){
		ops = mselOpts(dkey+'-'+val); 
		if(ops){ 
			s = mselElms(fmid,dkey,no+1,ops);
			$('#'+id).parent().append(s); 
		}
	}else{
		for(i=no+1;i<no+5;i++){
			$('#'+fmid+"_"+i).remove();
		}
	}

}
// 联动select-得到option
function mselOpts(dkey,vdef){
	var i,a,ops='';
	data = sordb_data(dkey);
	for(i=0;i<data.length;i++){
		a = data[i].split('=');
		if(a[0]){
			ops += "<option value='"+a[0]+"' "+(a[0]==vdef ? 'selected' : '')+">"+a[1]+"</option>";
		}
	}
	return ops;
}
// 联动select-得到select
function mselElms(fmid,dkey,no,ops){
	var id,a,op0,s;
	id = fmid+"_"+no; 
	if(jsElm.jeID(id)){
		$('#'+id).remove();
	}
	eval("a = window._msel_"+fmid+"_titles.split(',');");
	op0 = "<option value=''>-"+(a[no] ? a[no] : '请选择')+"-</option>";
	s = "<select id='"+id+"' onchange=\"mselAct('"+fmid+"','"+dkey+"',"+no+")\">"+op0+ops+"</select>";
	return s;
}

// localStorage/sessionStorage ===================================================================

/**
 * @class multiStore
 * @author Peace@txmao.com
 * @参考: http://www.cnblogs.com/zjcn/archive/2012/07/03/2575026.html#comboWrap
 * Demo: 
	var locStore = new multiStore('local');
	var sesStore = new multiStore('session');
	locStore.set('aa1','bb1');
	var tt1 = locStore.get('aa1');
	console.log(tt1);
	sesStore.set('aa2','bb2');
	var tt2 = sesStore.get('aa2');
	console.log(tt2);
 */
function multiStore(flag){ // local,session
	this.parFlag = flag=='session' ? 'sessionStorage' : 'localStorage';
	this.parStore = flag=='session' ? window.sessionStorage : window.localStorage;
	// 是否支持localStorage/sessionStorage
	this.ready = function(){ 
		return (this.parFlag in window) && (window[this.parFlag] !== null); 
	};
	// 扩展 : 最多设置保存mnum个key(如最近浏览历史记录)
	// demo: locStore|sesStore.setGroup('{$ckpre}chid{$chid}','{aid}',10); // ('_auto_dev52_chid2','542350',10); 
	// ??? 一条记录存储更多信息? 这里没有处理, 统一规范? 目前要扩展, 如类似信息【id|529026;time|14-07-2110:50】
	this.setGroup = function(keyid,nowkey,mnum){
		if(nowkey.length==0) return;
		if(!mnum) mnum = 10;
		var oldkeys = this.get(keyid); 
		if(!oldkeys){ 
			var keystr = nowkey;
		}else{ 
			var oldarr = oldkeys.split(','); 
			var keystr = nowkey; unum = 1;
			for(var i=0;i<oldarr.length;i++){ 
				if(oldarr[i]==nowkey || oldarr[i].length==0) continue;
				if(unum<mnum){
					keystr += ','+oldarr[i];	
					unum++;
				}else{
					break;	
				}
			}
		}
		keystr = keystr.replace(/[^0-9A-Za-z_\.\-\:\,\|\;]/g,''); // setGroup内容字符限制 \=\)\(\]\[  善用ascii码
		this.set(keyid,keystr);
	};
	// 扩展 : 初始化信息(不支持localStorage/sessionStorage)
	// demo: locStore|sesStore.initMessage('itemList','<li class="none">不支持localStorage/sessionStorage(本地存储)</li>')
	this.initMessage = function(id,msg){
		var canFlag = this.ready();
		if(!canFlag) document.getElementById(id).innerHTML = msg;
	};
	// 设置值
	this.set = function(key, value){
		//在iPhone/iPad上有时设置setItem()时会出现诡异的QUOTA_EXCEEDED_ERR错误；这时一般在setItem之前，先removeItem()就ok了
		if( this.get(key) !== null )
			this.remove(key);
		this.parStore.setItem(key, value);
	};
	// 获取值 查询不存在的key时，有的浏览器返回undefined，这里统一返回null
	this.get = function(key){
		var v = this.parStore.getItem(key);
		return v === undefined ? null : v;
	};
	this.each = function(fn){
		var n = this.parStore.length, i = 0, fn = fn || function(){}, key;
		for(; i<n; i++){
			key = this.parStore.key(i);
			if( fn.call(this, key, this.get(key)) === false )
				break;
			//如果内容被删除，则总长度和索引都同步减少
			if( this.parStore.length < n ){
				n --;
				i --;
			}
		}
	};
	this.remove = function(key){
		this.parStore.removeItem(key);
	}
	this.clear = function(){
		this.parStore.clear();
	};
	
}
