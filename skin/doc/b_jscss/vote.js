
// 通过id得到web元素
function voteWin(did,vid) {
  $.get(
      _cbase.run.roots+'/plus/coms/votet.php?did='+did+'&vid='+vid+'&'+_cbase.safil.url, 
      {name: '_r1'}, 
      function (ret) { 
        if(ret.error){
            layer.msg('错误:'+ret.msg, {icon: 1});
        }else{ //jsLog(ret.enc);
            layer.confirm('确认投票?', {
                btn: ['投票','取消'] //按钮
            }, function(){
                $.get(
                    _cbase.run.roots+'/plus/coms/votet.php?enc='+ret.enc+'', 
                    {name: '_r2'}, 
                    function (re2) { 
                        if(re2.error){
                              layer.msg('错误:'+re2.msg, {icon: 2});
                        }else{
                              $('#v_'+did+'_'+vid).html('['+re2.votes+']票');
                              layer.msg('投票成功!', {icon: 1});
                        }
                    }, 'jsonp'
                );
            }, function(){
                //layer.msg('取消了...');
            });
        }
      }, 'jsonp'
  );
}

function voteImg() { 
    $('.vote-order').find('tr').each(function(i,e){
        var img = $(this).find('img')[0];
        $(this).mouseover(function(){
            $(img).show();
            $(e).addClass('act');
        }).mouseout(function(){
            $(img).hide();
            $(e).removeClass('act');
        });
    });
}

function voteItem() { 
    var dno = window.location.hash.substr(1); 
    $('#dno_'+dno).addClass('c00F');
}
