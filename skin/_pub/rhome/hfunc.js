
var tipno = 0;

function tipActs(){ 
    $('.vnote p').hide();
    var len = $('.vnote p').length; 
    $('.vnote p').each(function(id, ele) {
        if(id==tipno%len) $(this).show();
    });
    tipno++;
    setTimeout('tipActs()',2300);
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

function winReset(ismob){
    //return; //winAutoMargin('ptop');
    var swin = winSize(); 
    var sdiv = jeSize('outer');    
    var tmpw = swin.w>300 ? swin.w-20 : swin.w; 
    var tmph = swin.h>350 ? swin.h-20 : swin.h;
    // bgimg: 1024x576
    if(tmpw>960) tmpw=960;
    $('#outer').width(tmpw);
    if(tmph>560){
        var tmph = parseInt((tmph-560)/2);
        $('.ptop').height(tmph);
        tmph = 560;
    }else{
        if(swin.h>350){
            $('.ptop').height(parseInt(8));
        }
    }
    if(swin.h>350){
        $('.ptop').show();
    }else{
        $('.ptop').hide();
    }
    $('#outer').height(tmph);
}

function adpCssText(ismoble){
    var s = new Array('','');
    s[0] = "nav { text-align:left; }";
    s[1] += "nav { text-align:center; }";
     return s[ismoble];
}

function impCssText(text,cssId){
    if($('#'+cssId)){ $('#'+cssId).remove(); }
    var style = document.createElement("style");    
    style.id = cssId;
    document.getElementsByTagName("head")[0].appendChild(style);
    if(style.styleSheet){ //for ie
        style.styleSheet.cssText = text;
    }else{ //for w3c
        style.appendChild(document.createTextNode(text));    
    }
}
//impCssText("#div{background-color:#F30;color:#FF;}","ucss01");
