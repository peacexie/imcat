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

function admJsClick(e){ 
    var p = jeFind(e,'a','prev'); 
    var url = $(p).prop('href')+'&view=form'; 
    $('#adf_main').prop('src',url);
}
function admSetTab(id,rst){
    var a = admNavTab.split(',');
    var b = admNavName.split(',');
    var c = admNavIcon.split(',');
    for(var i=1;i<a.length;i++){ 
        var o = jsElm.jeID('left_'+a[i]); 
        o.style.display = (id==a[i]) ? '' : 'none';
        if(id==a[i]){ 
            $('#adf_title').html("<i class='fa fa-"+c[i]+"'></i>"+b[i]); 
            if(!rst){
                var flnk = $('#left_'+a[i]+' a:first').attr('href'); 
                $('#adf_main').prop('src',flnk); 
            }
        }
    }
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
    admSetTab('m1adm',1); //setTimeout("",300); 
    window.onresize = function(){ admReSize(); }
    if(!admReSized) admReSize();
});

