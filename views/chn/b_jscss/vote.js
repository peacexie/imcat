

// 通过id得到web元素
function formWin(fmid,did) {
  // 单选,多选
  asel = (vsel+','+vmul).split(',');
  for(var i=0; i<asel.length; i++) {
      var k = asel[i], k2 = k.replace('[]','').replace(/\[/g,'_').replace(/\]/g,'_');
      if(!k) continue;
      var v = $('#topfm input[name="'+k+'"]:checked').val();
      if(!v){ alert('请选择:'+vtab[k2]); return; }
  }
  // 输入
  ainp = (vinp+','+varea).split(',');
  for(var i=0; i<ainp.length; i++) {
      var k = ainp[i], k2 = k.replace(/\[/g,'_').replace(/\]/g,'_');
      if(!k) continue;
      var v = $('#topfm input[name="'+k+'"]');
      if(!v.val()){ alert('请填写:'+vtab[k2]); v.focus(); return; }
  }
  var data = $("#"+fmid).serialize();
  $.ajax({
    url: _cbase.run.roots+'/plus/coms/formt.php?did='+did+'', 
    type: 'post',
    data: data,
    success: function (re2) { jsLog(re2);
        if(re2.error){
                layer.msg('错误:'+re2.msg, {icon: 2});
            }else{
                $('#v_').html('['+re2.votes+']票');
                layer.msg('提交成功!', {icon: 1});
                $("#"+fmid)[0].reset();
            }
        },
    dataType: 'jsonp'
  });

}

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
