
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
