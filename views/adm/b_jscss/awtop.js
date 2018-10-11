if(top.location!==self.location){top.location=self.location;}

function admShowTop(){
    if(_cbase.run.isMoble){
        admHtmTop = admHtmTop.replace(/<a/g,'<li><a').replace('</a>','</a></li>');
        $('#adf_nav1').prepend(admHtmTop);
    }else{
        admHtmTop = admHtmTop.replace(/<a/g,'<i class="cCCC">●</i><a');
        $('#adf_nav1').append(admHtmTop);
    }
}

function admSetTab(id,rst){
    var a = admNavTab.split(','),
        b = admNavName.split(','),
        c = admNavIcon.split(',');
    if(!id) id = _mpadm.defmu; // 无默认菜单
    // rst:权限判断, _mpadm.menus
    if(rst && _miadm.userGrade!=='supper'){
        admMnuPerm(id)
    }
    if(!id){ return } // 没有任何权限
    for(var i=1;i<a.length;i++){ 
        var o = jsElm.jeID('left_'+a[i]); 
        o.style.display = (id==a[i]) ? '' : 'none';
        if(id==a[i]){ 
            $('#adf_title').html("<i k='"+id+"' class='fa fa-"+c[i]+"'></i>"+b[i]);
        }
    }
}
function admMnuPerm(id){
    id = _mpadm.menus.indexOf(','+id+',') ? id : ''; //jsLog(id);
    $("#adf_left div").each(function(){
        var did = $(this).prop('id').replace('left_',''); 
        if(_mpadm.menus.indexOf(','+did+',')>0){ 
            if(!id) id = did;
        }else{ // Noperm
            $('#left_'+did).hide(); 
            var e = $('#adf_nav1 a.atm_'+did)[0];
            $(e).hide(); $(e).prev().hide();
        }
        $(this).find('ul').each(function(){
            var uid = $(this).prop('id').replace('left_','');
            if(_mpadm.menus.indexOf(','+uid+',')<0){
                $('#left_'+uid).hide(); 
            }
            $(this).find('li').each(function(){
                var iid = $(this).prop('id'); if(!iid) return true;
                iid = iid.replace('left_','');
                if(_mpadm.menus.indexOf(','+iid+',')<0){
                    $('#left_'+iid).hide();
                }
            });  
        });    
    });    
}

function admJsClick(e){
    var url = $(e).prop('href'), ext = '',
        target = $(e).attr('target');
    if(!url){
        var p = jeFind(e,'a','prev'),
            url = $(p).prop('href')+'&view=form'; 
        ext = $(p).text();
    }
    admLeftc(e, url, ext);
    if(!target || target=='adf_main'){
        $('#adf_main').prop('src',url);
        $(e).parent().trigger('click'); // in mobile, hidden pop menu
    }else if(target=='_parent'){
        location.href = url;
    }else{
        window.open(url);
    }
}
// click-保存
function admLeftc(e, url, ext){
    ext = ext ? '('+ext+')' : '';
    var rid = $('#adf_title i:first').attr('k'),
        pid = $(e).parent().parent().prop('id'),
        row = {rid:rid, rt:$('#adf_title').text(),
            pid:pid, pt:$('#'+pid).find('li:first').text(),
            title:ext+$(e).text(), url:url},
        str = JSON.stringify(row); //jsLog(str);
    admSloc.set('abcNav_last', str);
}

function admReSize(){
    if(_cbase.run.isMoble){
        var h = (winSize().h-40)+'px';
    }else{
        var h = (winSize().h-35)+'px';
        jsElm.jeID('adf_left').style.height = h;
    }
    jsElm.jeID('adf_main').style.height = h;
    jsElm.jeID('adf_right').style.height = h;
}

var admReSized = 0; 
jQuery(document).ready(function(){
    admShowTop(); 
    var _sleft = $('#adf_left').html();
    $('#adf_left').html(admHtmLeft+_sleft);
    admSetTab('',1); //setTimeout("",300);
    $("#adf_left>div a").each(function(){
        $(this).click(function(){
            admJsClick(this);
            return false;
        });
    });
    window.onresize = function(){ admReSize(); }
    if(!admReSized) admReSize();
});

