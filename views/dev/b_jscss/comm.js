
function jsactMenu(menuid){
    if(_cbase.jsrun.menuid){
        menuid = _cbase.jsrun.menuid;
    }else if(!menuid){
        var a = _cbase.run.mkv.replace('.','-').split('-');
        menuid = a[0];
    }
    var e = jsElm.jeID('idf_'+menuid); 
    if(e) e.className = 'active';
    // 处理链接
    $('.mod-section a').each(function(i, e) {
        var url = $(e).attr("href"); 
        if(url.indexOf('://')>0) $(e).attr("target",'_blank');
    });
}

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
