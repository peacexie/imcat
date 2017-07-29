
//---------------------------------------------------
// *** 基础函数
//--------------------------------------------------- \

// 获取文件名
function urlFile(){
    var url = this.location.href
    var pos = url.lastIndexOf("/");
    if(pos == -1){
       pos = url.lastIndexOf("\\")
    }
    var filename = url.substr(pos +1)
    return filename;
}
// 获取HTML页面参数 flag 为1 获取详细参数
function urlPara(key,def,url){
    url = url ? url : location.href;
    var re = (new RegExp("([^(&|\?)]*)" + key + "=([^(&|#)]*)").test(url+"#")) ? RegExp.$2 : '';
    if(def && !re) re = def;
    return re;
}
// ... 
function urlRep(url,key,val){
    if(url.indexOf('?')<=0) url += '?';
    if(key){
        url = url.replace(new RegExp(key + "=([^(&|#)]*)"),'');
        url += (val ? '&'+key+'='+val : '');
    }
    return url.replace('?&','?').replace('&&','&');
}
// Confirm
function urlConfirm(url,msg){
    if(msg=='go_url' || confirm(msg)){
        location.href = url;
        return true;
    }
    return false;
}
// urlEncode
function urlEncode(url,ext,percent){
    if(percent){
        url = url.replace('\\%','%25');    
    }
    var a = [ '\\#' , '\\&' ]; 
    var b = [ '%23' , '%26' ]; 
    var i;
    for(i=0; i<a.length;i++){
        url = url.replace(new RegExp(a[i],"g"),b[i]);
    };
    var c = [ '\\+' ,'\\ ' , '\\"' , "\\'" , '\\<' , '\\>' , "\\\r" , "\\\n" , "\\\\" ];
    var d = [ '%2B' ,'+'   , '%22' , '%27' , '%3C' , '%3E' , '%0D'  , '%0A'  , '%5C'  ];
    if(ext){
        for(i=0; i<c.length;i++){
            url = url.replace(new RegExp(c[i],"g"),d[i]);
        };
    } 
    return url;
}
// 按比例显示图片
function imgShow(obj,w,h){
    img = new Image(); img.src = obj.src; 
    zw = img.width; zh = img.height; 
    zr = zw / zh;
    if(w){ fixw = w; }
    else { fixw = obj.getAttribute('width'); }
    if(h){ fixh = h; }
    else { fixh = obj.getAttribute('height'); }
    if(zw > fixw) {
        zw = fixw; zh = zw/zr;
    }
    if(zh > fixh) {
        zh = fixh; zw = zh*zr;
    } 
    obj.width = zw; obj.height = zh;
}
// Json对象合并：var c=mjson(a,b,1); mjson({},[a,b]);
function jmJson(des, src, override){
    if(src instanceof Array){
        for(var i=0, len=src.length; i<len; i++)
             jmJson(des, src[i], override);
    }
    for(var i in src){
        if(override || !(i in des)){
            des[i] = src[i];
        }
    } 
    return des;
}
// int to hex
function toHex(n){    
    var h, l;
    n = Math.round(n);
    l = n % 16;
    h = Math.floor((n / 16)) % 16;
    return (tabHex1[h] + tabHex1[l]);
}

// getBrowser
function getBrowser(){ 
    // Safari, Android, Opera, Chrome, MSIE
    // MSIE, Firefox, Chrome, Safari, Opera
    var b = {}; 
    //b.WebKit = jsTest('Webkit');
    //b.Gecko = jsTest('Gecko') && !jsTest('Webkit'); 
    //b.Trident = jsTest('Trident');
    b.Safari = jsTest('Safari') && !jsTest('Chrome'); 
    b.Opera = jsTest('Opera');
    b.Firefox = jsTest('Firefox');
    b.Android = jsTest('Android');
    b.Chrome = jsTest('Chrome');
    b.IE = jsTest('Trident') || jsTest('MSIE');
    b.IE6 = jsTest('MSIE 6');
    b.IE7 = jsTest('MSIE 7');
    b.IE8 = jsTest('MSIE 8');
    return b;
}
function setEvent(onAct,doAct,ID,tag) {
    var oItems = jsElm.jeID(ID).getElementsByTagName(tag);
    for(var i = 0;i<oItems.length;i++){
        oItems[i].setAttribute(onAct,doAct+"(this)"); //IE8,isFirefox
    }
}
// jqInit
function jqInit(data){
    eval("jQuery(document).ready(function(){"+data+"});");
}
// jqHtml,支持eid=jsid_count_nrem:2015-9g-n301
function jqHtml(eid,html){
    jQuery(jsElm.jeID(eid)).html(html);
}
// jqPcpr
function jqPcpr(eid){
    jQuery(jsElm.jeID(eid)).parent().css('position','relative');
}

var jsElm = {}; //通过id得到web相关元素
jsElm.pdID = function(id){ // 获取iframe父窗口id对应的web元素
    return typeof id == 'string' ? parent.document.getElementById(id) : id; 
}
jsElm.ifID = function(id){ // 获取iframe，id为iframe的对应ID
    return document.getElementById(id).contentWindow;
    //return window.frames[id]; //id为name值
}
jsElm.jeID = function(id){ // 通过id得到web元素
    return typeof id == 'string' ? document.getElementById(id) : id;
}
jsElm.jeTag = function(tag){ // 通过tag名称得到web元素(第一个),tag=body,head,p
    return document.getElementsByTagName(tag)[0];
}

// 查找e元素的前一个/下一个/父元素,且元素名称为tag
function jeFind(e,tag,type) {
    e = jsElm.jeID(e); var f;
    if(type=='prev') f = e.previousSibling;
    else if(type=='next') f = e.nextSibling;
    else f = e.parentNode; 
    try{
        while(f.nodeType==3){
            if(type=='prev') f = f.previousSibling;
            if(type=='next') f = f.nextSibling;
        }
    }catch(ex){ return null; }
    if(f.tagName.toLowerCase()==tag) return f;
    else return jeFind(f,tag,type);
}
// 显示/隐藏e元素
function jeShow(xID){
  var elm = jsElm.jeID(xID); 
  if(!elm) return;
  if(elm.style.display=='none') { elm.style.display = ''; }
  else { elm.style.display = 'none'; } 
} 
// 居中显示div，xOffset:百分比
function jeCenter(xID,xOffset){
    var elm = jsElm.jeID(xID); 
    var oSize = jeSize(xID);
    var wSize = winSize();
    var top=0,left=0;
    top = (wSize.winHeight-oSize.height)/2;
    left = (wSize.winWidth-oSize.width)/2;
    if(xOffset) top = top - parseInt(top*xOffset/100);
    elm.style.top = top + "px";
    elm.style.left = left + "px";
}
// 元素大小
function jeSize(xID){
    var elm = jsElm.jeID(xID); 
    var obj = {};
    obj.width = elm.offsetWidth;
    obj.height = elm.offsetHeight;
    return obj;
} 
//获取元素的位置 
function jePos(obj) { 
    var pos = $('#'+obj).offset();
    return [pos.left,pos.top];
    var obj = jsElm.jeID(obj); 
    if (obj == null) return null; 
    var posLeft = obj.offsetLeft; 
    var posTop = obj.offsetTop; 
    //while (obj != null && obj.offsetParent != null && obj.offsetParent.tagName != "BODY") { 
        //posLeft = posLeft + obj.offsetParent.offsetLeft; 
        //posTop = posTop + obj.offsetParent.offsetTop; 
    //} 
    return [posLeft,posTop]; 
}; 

function isObj(obj,type){ 
    var cons = obj.constructor.toString();
    if(type=='a') type='Array';
    if(type=='b') type='Boolean';
    if(type=='f') type='Function';
    if(type=='n') type='Number';
    if(type=='s') type='String';
    if(!type) type = 'Object';
    return (cons.indexOf(type)!= -1);
}
// 正则测试str,如判断ie6浏览器
function jsTest(c,str) {  
    if(!str) str = navigator.userAgent; 
    if(!c) c = 'MSIE 6';
    else if(!isNaN(c)) c = 'MSIE '+c; 
    var pos = str.indexOf(c);
    return pos<0 ? false : true; //reg.test(str);
}
// jsKeys
function jsKey(fid){
    var a = new Array("[",']',' ','/','-','.','&','=','#','?');
    var b = new Array("_",'_','_','_','_','_','_','_','_','_');
    for(var i=0;i<a.length;i++){
        fid = fid.replace(a[i],b[i]).replace(a[i],b[i]).replace(a[i],b[i]); 
    }
    return fid;
}
// jsReplace
function jsRep(str){
    var a = new Array("'",'"','\n','\r');
    var b = new Array("\\'",'\\"','\\n','\\r');
    for(var i=0;i<a.length;i++){
        str = str.replace(new RegExp(a[i],'g'),b[i]);
    }
    return str;
}
// removeHTMLTag
function jsText(str){ 
    str = str.replace(/<\/?[^>]*>/g,''); //去除HTML tag
    str = str.replace(/[ | ]*\n/g,'\n'); //去除行尾空白
    //str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
    str=str.replace(/&nbsp;/ig,'');//去掉&nbsp;
    return str;
}
// lang('jcore.jstag_toolong',id)
// {lang(jcore.sys_name)}
function lang(mk, val){ 
    if(mk.indexOf('.')<=0) mk = "jcore."+mk;
    try{ 
        vre = eval("Lang."+mk); 
    }catch (ex1){ return '{'+mk+'}'; }
    if(typeof(val)=="undefined") return vre;
    vre = vre.replace('{val}',val); 
    return vre;
}
// 动态导入Js/CSS文件
function jsImp(sFile,basePath,cbk){     
    if(_cbase.run.jsimp.indexOf(sFile)<=0) _cbase.run.jsimp += ','+sFile;
    else return;  
    if(typeof(basePath)=='undefined') basePath = _cbase.run.roots;
    if(basePath.length==0) basePath = _cbase.run.roots;
    sFile = basePath + sFile; 
    if(cbk=='{code}'){
        var ext = sFile.substr(sFile.length-4);
        var cjs = "<script src='"+sFile+"'></script>";
        if(ext=='.css'){ cjs="<link href='"+sFile+"' rel='stylesheet' type='text/css'/>"; }
        document.write(cjs);
    }else{
        jQuery.getScript(sFile,function(){ cbk && cbk.call(); }); 
    }
}

// 检测str是否定义,
// 如定义: 返回true,str值
// 未定义: 返回false,''
function jsVar(str, debug){ 
    var re = new Array(false,'');
    try{ 
        eval('var temp = '+str+';');
        re[0] = true;
        re[1] = temp;
    }catch(e){ 
        if(debug) alert('jsVar()'); 
    }
    return re;
}
// runData
function jsTry(data,debug){
    try{ eval(data); }
    catch(ex){ if(debug) alert('jsTry()'); }
}

function jsEval(str){
    if(isObj(str,'f')){
        str();
    }else if(str.indexOf('(')>0){ 
        eval(fn);
    }else{
        eval(fn+'();'); 
    }
}
// 调试信息
function jsLog(msg,color){
    // ?? debug模式
    if(window.console){ 
        var cstr = '';
        if(color){
            var cstr = 'color:'+color;
            msg = '%c'+msg;
        }
        console.log(msg,cstr);
    }else{
        alert(JSON.stringify(msg)); 
    }
}
// 调试时间
function jsTime(flag,end){
    if(end) console.timeEnd(flag);
    else console.time(flag);    
}
// jsRnd
function jsRnd(flag,iMax){
    if(iMax) return Math.floor(flag+Math.random()*(iMax-flag));
    if(!flag) flag = '_r';
    var r = new Date().getTime(); //Math.random();
    var s = flag+'='+r;
    try{ s += '&lang='+_cbase.sys.lang; }
    catch(ex){ }
    return s;
}
