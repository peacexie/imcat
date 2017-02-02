
$(function(){
    $('.adpush_obj').mouseenter(function(e) {
        var pobj = this, ph = $(pobj).height()/2-15; // 66x25
        var ibtn = '<div class="adpush_edit"><a style="margin-top:'+ph+'px;">['+lang('push.ps_pinfo')+']</a></div>';
        $(ibtn).appendTo(this).find('a').click(function(e) {
            var pars = '&aid='+$(pobj).attr('aid')+'&omkv='+_cbase.run.mkv+'&otpl='+_cbase.run.tpldir+'';
            var url = _cbase.run.roots+'/run/adm.php?file=dops/a&mod=adpush&view=push'+pars;
            var ops = {type:2, fix:false, maxmin:true, title:[lang('push.ps_pinfo'),true], area:['750px','560px'], content:url};
            layer.open(ops);
        });
    }).mouseleave(function(e) {
        $(this).find('.adpush_edit').remove();
    });
});
