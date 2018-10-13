
// Search - Start
function schEnc(s){
    return s.replace(/&/g,"&").replace(/</g,"<").replace(/>/g,">").replace(/([\\\.\*\[\]\(\)\$\^])/g,"\\$1");
}
function schDec(s){
    return s.replace(/\\([\\\.\*\[\]\(\)\$\^])/g,"$1").replace(/>/g,">").replace(/</g,"<").replace(/&/g,"&");
}    
function schDone(s){
    var s = s ? s : jsElm.jeID('schVal').value;
    if (s.length==0){
        alert(lang('jcore.so_keyword'));
        return false;
    }
    s=schEnc(s); 
    var obj=document.getElementsByTagName("body")[0];
    var t=obj.innerHTML.replace(/<span\s+class=.?highlight.?>([^<>]*)<\/span>/gi,"$1");
    obj.innerHTML=t;
    var cnt=schLoop(s,obj);
    t=obj.innerHTML
    var r=/{searchHL}(({(?!\/searchHL})|[^{])*){\/searchHL}/g
    t=t.replace(r,"<span class='highlight'>$1</span>");
    obj.innerHTML=t;
    alert(lang('jcore.so_result',cnt));
}
function schLoop(s,obj){
    var cnt=0;
    if (obj.nodeType==3){
        cnt=schAddTags(s,obj);
        return cnt;
    }
    for (var i=0,c;c=obj.childNodes[i];i++){
    if (!c.className||c.className!="highlight")
        cnt+=schLoop(s,c);
    }
    if(cnt>1) cnt--;
    return cnt;
}
function schAddTags(s,dest){
    var r=new RegExp(s,"g");
    var tm=null;
    var t=dest.nodeValue;
    var cnt=0;
    if (tm=t.match(r)){
        cnt=tm.length;
        t=t.replace(r,"{searchHL}"+schDec(s)+"{/searchHL}")
        dest.nodeValue=t;
    }
    return cnt;
}
// Search - End
function schCase(e,ex1,ex2,dir,key){
    ex1 = '('+ex1+')'; ex2 = '('+ex2+')';
    chk = (e.checked==true)?true:false;
    itm = document.getElementsByTagName('input');
    for(var i=0;i<itm.length;i++){
        id = itm[i].id.toString(); //if(i<3)alert(id);
        v = itm[i].value; //.schAddTags('.','')
        if(id.indexOf('dir')>=0){ 
            itm[i].checked = false;
            if(dir.indexOf(v)>=0) itm[i].checked = chk;
        }
        if(id.indexOf('ex1')>=0){ 
            itm[i].checked = false;
            if(ex1.indexOf(v)>0) itm[i].checked = chk;
        }
        if(id.indexOf('ex2')>=0){ 
            itm[i].checked = false;
            if(ex2.indexOf(v)>0) itm[i].checked = chk;
        }
        if(id.indexOf('key')>=0){ 
            okey = document.getElementById('key');
            if(okey.value.length==0) okey.value = key;
            else{
                okey.value = okey.value.replace('$','').replace('function ','').replace('class ','')
                if(okey.value.indexOf(key)<0) okey.value = key+okey.value;
            }
        }
    }    
}
