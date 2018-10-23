
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

function qrActs(){
    $('div.nav').on('mouseover','a',function(){
        var href = $(this).prop('href');    
        var mod = $(this).find('i').prop('id').replace('qrcode_pic','');    
        qrurl_act(mod,1,href);
    });
    $('div.nav').on('mouseout','a',function(){
        var mod = $(this).find('i').prop('id').replace('qrcode_pic','');    
        qrurl_act(mod,0);
    });
    $('.qrcode_home').on('mouseover','',function(){
        var href = $(this).attr('_url');
        qrurl_act('home',1,_burl+href);
    });
    $('.qrcode_home').on('mouseout','',function(){
        qrurl_act('home',0);
    });
}
