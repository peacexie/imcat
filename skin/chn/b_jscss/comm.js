function js_cklogin(id){
    var sinf, ainf='';
    if(_minfo.userFlag=='Guest'){
        var uname = 'Guest';
        sinf = '<span class="uname">'+uname+'</span> 您好！<br><a href="'+umc_url+'?mkv=user-login&recbk=ref">马上登陆…</a>';
    }else{
        var uname = _minfo.uname;
        if(uname.length>9){
            uname = uname.substr(0,6)+'...'+uname.substr(-3,3);
        }
        sinf = '<span class="uname" title="'+_mperm.title+'">'+uname+'</span> 您好！<br><a href="'+umc_url+'?mkv=user-login&act=doout&recbk=ref" target="_blank">登出…</a>';
    }
    if(_miadm.userFlag=='Login'){ 
        ainf = '<a class="cF0F" href="'+_cbase.run.roots+'/run/adm.php?mkv=login&act=doout" target="_blank" title="登出:'+_mpadm.title+'">登出:'+_miadm.uname+'</a>';
    }
    if(typeof(ocar_items)=="undefined"){
        var citems = getCookie('ocar_items');
    }else{
        var citems = ocar_items;
    }
    citems = citems ? citems : 0; 
    ainf += '<br><a class="cF0F" href="'+ocar_url+'">购物车('+citems+')</a>';
    if(!id) id = 'top_cklogin';
    if(id) $('#'+id).html("<span class='ainf'>"+ainf+"</span>"+sinf);
    return sinf+ainf;
}

function jsactMenu(menuid){
    if(_cbase.jsrun.menuid){
        menuid = _cbase.jsrun.menuid;
    }else if(!menuid){
        var a = _cbase.run.mkv.replace('.','-').split('-');
        menuid = a[0];
    }
    var e = jsElm.jeID('idf_'+menuid); 
    if(e) e.className = 'act';
}

function js_i18nbar(){ 
    jQuery.getScript(_cbase.run.rskin+'/_pub/a_jscss/i18nbar.js',function(){ 
        i18nb_mui('Chinese','i18nb_obj','i18nb_api','i18nb_btn'); 
    });
}

function js_aheight(){ 
    if($('.pgf_side2').html()){
        if($('.pgf_mcon2').height()+20>$('.pgf_mid2').height()){
            $('.pgf_mid2').height($('.pgf_mcon2').height()+20); 
        }        
    }else{
        if($('.pgf_side').height()>$('.pgf_mid').height()){
            $('.pgf_mid').height($('.pgf_side').height()); 
        }
    }
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
