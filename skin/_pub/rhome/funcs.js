
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
