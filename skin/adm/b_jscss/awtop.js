if(top.location!==self.location){top.location=self.location;}

function admShowTop(){
    if(_cbase.run.isMoble){
        admHtmTop = admHtmTop.replace(/<a/g,'<li><a').replace('</a>','</a></li>');
        admHtmTop = admHtmTop.replace(/\">/g,'\"><i class="glyphicon glyphicon-book"></i> ');
        $('#adf_nav1').prepend(admHtmTop);
    }else{
        $('#adf_nav1').append(admHtmTop);
    }
}

function admJsClick(id){ 
    var _add = jsElm.ifID('adf_main').jsElm.jeID(id+'_add'); 
    if(_add){ _add.click(); }
    else{ layer.alert(lang('adm.open_nowmod_list')); } 
}
function admSetTab(id,rst){
    var a = admNavTab.split(',');
    var b = admNavName.split(',');
    for(var i=1;i<a.length;i++){ 
        var o = jsElm.jeID('left_'+a[i]); 
        o.style.display = (id==a[i]) ? '' : 'none';
        if(id==a[i]){ 
            $('#adf_title').html(b[i]); 
            if(!rst){
                var flnk = $('#left_'+a[i]+' a:first').attr('href'); 
                $('#adf_main').prop('src',flnk); 
            }
        }
    }
}

function admReSize(){
  if(!_cbase.run.isMoble){
    var h = (winSize().h-26)+'px';
    jsElm.jeID('adf_left').style.height = h;
  }else{
    var h = (winSize().h-40)+'px';
  }
  jsElm.jeID('adf_main').style.height = h;
  jsElm.jeID('adf_right').style.height = h;
}

var admReSized = 0; 
jQuery(document).ready(function(){
    admShowTop(); 
    var _sleft = $('#adf_left').html();
    $('#adf_left').html(admHtmLeft+_sleft);
    admSetTab('m1adm',1); //setTimeout("",300); 
    window.onresize = function(){ admReSize(); }
    if(!admReSized) admReSize();
});

