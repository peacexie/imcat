function js_caritems(id){
    if(typeof(ocar_items)=="undefined"){
        var citems = getCookie('ocar_items');
    }else{
        var citems = ocar_items;
    }
    if(!citems) citems = 0;
    $('#top_scnt').html('('+(citems?citems:0)+')');
}


// qas_(id/key), qat_(id/key)
/*
function jsactMfaqs(){
    var e = jsElm.jeID('qas_'+qas_id); 
    if(e) e.className = 'act';
    var e = jsElm.jeID('qat_'+qat_id); 
    if(e) e.className = 'act';
    //else jsElm.jeID('qat_all').className = 'act';
}*/

function jsactMenu(menuid){
    if(_cbase.jsrun.menuid){
        menuid = _cbase.jsrun.menuid;
    }else if(!menuid){
        var a = _cbase.run.mkv.replace('.','-').split('-');
        menuid = a[0];
    }
    var e = jsElm.jeID('idf_'+menuid); 
    if(e) e.className = 'active';
}

function js_i18nbar(){ 
    jQuery.getScript(_cbase.run.rskin+'/_pub/a_jscss/i18nbar.js',function(){ 
        i18nb_mui('Chinese','i18nb_obj','i18nb_api','i18nb_btn'); 
    });
}

// <b class="qrcode_tip" onMouseOver="qrurl_act(id,1)" onMouseOut="qrurl_act(id,0)">扫码<i class="qrcode_pic"></i></b
function qrurl_set(id,url){ 
    cls = 'qrcode_pic';
    if($('#'+cls+id).html().length>24) return;
    url = url.length>12 ? url : window.location.href;
    url = urlEncode(url);
    img = "<img src='"+_cbase.run.roots+"/plus/ajax/vimg.php?mod=qrShow&data="+url+"' width='180' height='180' />";
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
                img = _cbase.run.roots+"/plus/ajax/vimg.php?mod=qrShow&data="+url; 
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

function mpro_vbig(e){
    var src = $(e).prop('src');
    $('#picBig').html("<img src='"+src+"' width=400 height=300 data-val='"+src+"' onload='imgShow(this,400,300)'>");
}
