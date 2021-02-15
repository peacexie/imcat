
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
    var width = width ? width : 640,
        height = height ? height : 480,
        scw = screen.width,
        sch = screen.height;
    if(width>scw) width = scw;
    if(height>sch) height = sch;
    var url, title, wt=_cbase.sys_open;
    if(isObj(e,'s')){
        url = e;
        title = title ? title : "winOpen";
    }else{ //a
        url = e.href;
        title = title ? title : e.innerHTML;    
    }
    url += (url.indexOf('?')>=0 ? '' : '?') + '&' + jsRnd('dialog');
    if(wt==1){ 
        var _x = (scw-width)/2, _y = (sch-height)/2, id = ext ? '_win_'+jsKey(ext) : '_win_';
        var p = ",left="+_x+",top="+_y+",width="+width+",height="+height+"";
        window.open(url,id,'scrollbars=yes,toolbar=no,location=no,status=no,menubar=no,resizable=yes'+p); 
    }else{ // 4
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
    } 
    return {w:w,h:h};
}
// 
function winAutoMargin(div,gap){
    if(!div) div = 'topMargin';
    if(!gap) gap = 20;
    wv = winSize('win'); 
    wc = winSize('out'); 
    if(wv.h>wc.h+gap){ 
        jQuery('#'+div).show();
        jQuery('#'+div).height((wv.h-wc.h-gap/2)*0.33);
    }else{
        jQuery('#'+div).hide();    
    }
}

// 前后台 表单 ===================================================================================================

function goPjump(e){
    var p = $(e).val();
    p = isNaN(p) ? 1 : parseInt(p);
    m = parseInt($(e).attr('pjmax'));
    if(p>m){ p = m; } 
    url = $(e).attr('pjurl').replace('&page=0&','&page='+p+'&'); 
    window.location.href = url;
}
function fsPos(fmid,pos){
    var x=0, y=0;
    var box = $('#'+fmid+'_vBox').prev();
    var samp = $('#'+fmid+'_vBox samp:first');
    var objw = $(box).outerWidth();
    var objh = $(box).outerHeight();
    var boxh = $(samp).outerHeight(); 
    if(pos && pos.indexOf(',')>0){
        var tmp = pos.split(',');
        x = tmp[0];
        y = tmp[1];
    }else{ // top
        var type = $(box).css('display'); 
        if(type=='block'){
            x = '0'; //+objw;
            y = '-'+(boxh+objh+3);
        }else{ // inline-block
            x = '-'+objw;
            y = '-'+(boxh+5);
        }
    }
    $(samp).css({'left':x+'px','top':y+'px'});
}
// 认证码初始化(认证码:onFocus触发)
function fsCode(fmid,reLoad,pos){
    var box = $('#'+fmid+'_vBox');
    if($(box).html().length<24){
      var img = '<samp class="fs_vimg_span" style="">';
      img += '<img id="'+fmid+'_vimg" src="'+_cbase.run.rskin+'/base/assets/aimg/blank.gif" onclick=\'fsCode("'+fmid+'","reLoad","'+pos+'")\' title="'+lang('jcore.vcode_upd')+'" />';
      img += '<samp class="fs_vimg_close" onclick=\'jeShow("'+fmid+'_vBox")\' title="'+lang('jcore.hide')+'">[X]</samp></samp>';
      $(box).html(img); 
      reLoad = 1;
    }
    if($(box).css('display')=='none') $(box).css('display','');
    fsPos(fmid,pos);
    //超时检测...
    var fimg = jsElm.jeID(fmid+'_vimg');
    if(reLoad){ 
        var fimg = jsElm.jeID(fmid+'_vimg');
        var para = "&mod="+fmid+"&"+jsRnd()+"&"+_cbase.safil.url;
        fimg.setAttribute("src",_cbase.run.fbase+"?ajax-vimg"+para);
    }
    $("p#evf_vtip").remove(); //onFocus触发后,保证把前一个tip清理掉,否则挡住了认证码.
}
// 重新加载-安全配置项 ??? 
function fsReLoad(fmid){
    var js = jsElm.jeID('jssrc_'+fmid);
    js.src += 1;
}
// 表单认证码
function fsInit(fmid,pos,css1,css2,tabi){
    url = _cbase.run.fbase+'?ajax-cajax&act=fsInit&fmid='+fmid;
    if(pos) url += '&pos='+pos; 
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
    jQuery("input.cbg_"+part).each(function() {
        jQuery(this).prop("checked", e.checked); 
    });    
}

// 前台ajax调用资料使用 ===================================================================================================

function jcUrlfmt(str, noenc){
    if(!str) return '';
    str = str.replace(/(^\s+)|(\s+$)/g, ''); // 前后空格
    if(noenc){ return str; }
    str = str.replace(/(\#)/g, '%23');
    str = str.replace(/(\&)/g, '%26');
    str = str.replace(/(\=)/g, '%3D');
    str = str.replace(/(\?)/g, '%3F');
    // #&=? -=> %23%26%3D%3F
    str = str.replace(/(\ )/g, '%20');
    str = str.replace(/(\\)/g, '%5C');
    return str; 
}
function jcronRun(tpldir,mkv,reurl){
    tpldir = tpldir ? tpldir : ((typeof(_cbase.run.tpldir)=='undefined') ? '' : _cbase.run.tpldir);
    mkv = mkv ? mkv : ((typeof(_cbase.run.mkv)=='undefined') ? urlPara('mkv','') : _cbase.run.mkv);
    var uri = jcUrlfmt(window.location.href),
        ref = jcUrlfmt(document.referrer),
        ua = jcUrlfmt(navigator.userAgent),
        sotab = /(Bot|Crawl|Spider|sohu-search|lycos|robozilla)/ig, // 搜索引擎agent, |slurp|okhttp
        soag = ((ua && sotab.test(ua)) || (!ref && (!ua || ua.length<15))) ? 1 : 0,
        pamx = '&soag='+soag+'&uri='+uri+'&ref='+ref+'&';
    var url = '?ajax-cron&tpldir='+tpldir+'&rf='+mkv+'&'+_cbase.safil.url+pamx+jsRnd();
    if(reurl) return url;
    jsImp(url); 
}
function jtagSend(){
    var uparas = '', ubase = window.location.pathname; //'http://'+window.location.host+
    var upath = '?ajax-jshow&tpldir='+_cbase.run.tpldir+'&rf='+_cbase.run.mkv+'&'+jsRnd(); 
    jQuery("[id^='jsid_']").each(function(){
        var id = this.id, val = jtagPara(this), data = '&'+id+'='+val; 
        if(id.length>12){ 
            if(uparas.length+data.length>1800){ // 2038
                jsImp(upath+uparas);
                uparas = data;
            }else{
                uparas += data;
            } 
        }
    });
    if(uparas.indexOf('&jsid')>=0){ 
        jsImp(upath+uparas); 
    }
}
function jtagPara(e){
    var s = jQuery(e).html().replace('0<!--','').replace('<!--','').replace('-->','');     
    s = urlEncode(s,1,1);
    return s;
}
function jtagRep(id,rep){
    jQuery('#'+id).after(rep);
    jQuery('#'+id).remove();
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
    op0 = "<option value=''>-"+(a[no] ? a[no] : lang('jcore.pleaseselect'))+"-</option>";
    s = "<select id='"+id+"' onchange=\"mselAct('"+fmid+"','"+dkey+"',"+no+")\">"+op0+ops+"</select>";
    return s;
}

// localStorage/sessionStorage ===================================================================

/**
 * @class mulStore
 * @参考: http://www.cnblogs.com/zjcn/archive/2012/07/03/2575026.html#comboWrap
 * Demo: 
    var sloc = new mulStore('local'); sloc.set('aa1','bb1');
    var sess = new mulStore('session'); var tt2 = sess.get('aa2');
    function fms(k,v){jsLog(k+'(=)'+v);} sloc.each(fms);
 */
function mulStore(flag){ // local,session
    this.key = flag=='session' ? 'sessionStorage' : 'localStorage';
    this.Store = window[this.key];
    // 是否支持 
    this.ready = function(){ 
        return (this.key in window) && (window[this.key] !== null); 
    };
    // 扩展 : 初始化不支持的提示信息
    // demo: obj.init('itemList','不支持本地存储')
    this.init = function(id,msg){
        var canFlag = this.ready();
        if(!canFlag) document.getElementById(id).innerHTML = msg;
    };
    // 扩展 : 最多设置保存mnum个key(如最近浏览历史记录)
    // demo: obj.mset('{=$pre}mod{=$mod}','{did}',10); // ('_imcat_news','2017-3q-abcd',5); 
    this.mset = function(keyid,nowkey,mnum){
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
        keystr = keystr.replace(/[^0-9A-Za-z_\.\-\:\,\|\;]/g,''); // 内容字符限制 \=\)\(\]\[
        this.set(keyid,keystr);
    };
    // 设置值
    this.set = function(key, value){
        key = this.kid(key);
        //在iPhone/iPad上有时设置setItem()时会出现诡异的QUOTA_EXCEEDED_ERR错误；
        if( this.get(key) !== null ) this.del(key);
        this.Store.setItem(key, value);
    };
    // 获取值 查询不存在的key时，有的浏览器返回undefined，这里统一返回null
    this.get = function(key){
        var v = this.Store.getItem(this.kid(key));
        return v === undefined ? null : v;
    };
    this.del = function(key){
        this.Store.removeItem(this.kid(key));
    };
    this.clear = function(){
        this.Store.clear();
    };
    this.each = function(fn){
        var n = this.Store.length, i = 0, fn = fn || function(){}, key;
        for(; i<n; i++){
            key = this.Store.key(i);
            if( fn.call(this, key, this.get(key)) === false ) break;
            //如果内容被删除，则总长度和索引都同步减少
            if( this.Store.length < n ){
                n--; i--;
            }
        }
    };
    this.kid = function(key){
        return _cbase.ck.ckpre+jsKey(key);
    };
}
