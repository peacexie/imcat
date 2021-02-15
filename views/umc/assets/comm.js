
// <b class="qrcode_tip" onMouseOver="qrurl_act(id,1)" onMouseOut="qrurl_act(id,0)">扫码<i class="qrcode_pic"></i></b
function qrurl_set(id,url){ 
    cls = 'qrcode_pic';
    if($('#'+cls+id).html().length>24) return;
    url = url.length>12 ? url : window.location.href;
    url = urlEncode(url);
    img = "<img src='"+_cbase.run.fbase+"?ajax-vimg&mod=qrShow&data="+url+"' width='180' height='180' />";
    $('#'+cls+id).html('扫描网址到手机<br>'+img); // onload='imgShow(this,180,180)'
}
function qrurl_act(id,type,url){
    if(url) qrurl_set(id,url);
    if(type) $('#qrcode_pic'+id).show();
    else $('#qrcode_pic'+id).hide();
}

function qrcargo_act(id,type,url){
    if(url){
         var src = $('#qrcode_pic'+id).attr('src');
         if(!src){
            if(url.indexOf('?')>=0){ //08tools/yssina/1/root/run/mob.php?cargo.2015-97-dad1
                url = _cbase.run.rsite+url; 
                img = _cbase.run.fbase+"?ajax-vimg&mod=qrShow&data="+url; 
                $('#qrcode_pic'+id).find('img').attr('src',img);
            }else{//cargo.2015-97-dad1
                var extp = Math.random().toString(36).substr(2); 
                extp = url+','+extp;
                var url = 'actys=getQrcode&qrmod=send&extp='+extp+'&datatype=js&varname=data';
                $.getScript(_cbase.run.roots+'/plus/api/wechat.php?'+url, function(){ 
                    img = data.url; 
                    $('#qrcode_pic'+id).find('img').attr('src',img);
                });    
            }
         }
    } 
    if(type) $('#qrcode_pic'+id).show();
    else $('#qrcode_pic'+id).hide();
}

/* --- */

function log(msg){
    alert(JSON.stringify(msg));
    console.log(msg);
}
function eid(id){
    return document.getElementById(id);
}
function stext(id, reval=0){
    //return $('#'+id).find("option").not(function(){ return !this.selected }).text();
    var obj = eid(id); //定位id
    var index = obj.selectedIndex; // 选中索引
    var text = obj.options[index].text; // 选中文本
    var value = obj.options[index].value; // 选中值
    return reval ? value : text;
}

function setName(kid,kname){ 
    var idstr = $("input[name='"+kid+"'").val(), nmstr = '',
        idarr = idstr ? idstr.split(',') : []; //console.log(idstr,idarr);
    for(var i=0;i<idarr.length;i++){
        if(!idarr[i]){ continue; }
        nmstr += (nmstr?',':'') + (idarr[i] in utab ? utab[idarr[i]]['name'] : '('+idarr[i]+')');
    } 
    $("input[name='"+kname+"'").val(nmstr);
}

var jsApiListDef = [
    'checkJsApi',
    'onMenuShareAppMessage',
    'onMenuShareWechat',
    'onMenuShareTimeline',
    'shareAppMessage',
    'shareWechatMessage',
    'chooseImage',
    'previewImage',
    'uploadImage',
    'downloadImage',
    //'getNetworkType',
    'openLocation',
    'getLocation',
    'hideOptionMenu',
    'showOptionMenu',
    'hideMenuItems',
    'showMenuItems',
    //'hideAllNonBaseMenuItem',
    //'showAllNonBaseMenuItem',
    'closeWindow',
    //'scanQRCode',
    'previewFile',
    'openEnterpriseChat',
    'selectEnterpriseContact',
    'onHistoryBack',
    'openDefaultBrowser'
]; // 所有要调用的 API 都要加到这个列表中

