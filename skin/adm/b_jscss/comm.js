
function jsactMenu(menuid){
    if(!menuid){
        var a = _cbase.run.mkv.replace('.','-').split('-');
        menuid = a[0];
    }
    var e = jsElm.jeID('idf_'+menuid); 
    if(e) e.className = 'act';
}

function setEdit(disfms,hdrows,fmfix){
    if(!fmfix) fmfix = 'fm';
    var arr = new Array(disfms,hdrows);
     for(var j=0;j<arr.length;j++){
        var afm = arr[j].split(',');
        for(var i=0;i<afm.length;i++){
            var e1 = jsElm.jeID(fmfix+'['+afm[i]+']');
            var e2 = jsElm.jeID(fmfix+'_'+afm[i]+'_');
            if(j==0){
                $(e1).prop('disabled',true);
                $(e2).prop('disabled',true);
            }else{
                $(e1).parent().parent().hide();
                $(e2).parent().parent().hide();
            }
        }
     }
}

// var tpl = "<a href='?mkv=dops-a&mod=topic&did={id}&view=cfgs' target='_blank'>资料管理</a>";
// setColstr('tblist',-2,'资料管理',tpl);
function setColstr(tabid,cno,title,tpls,flag){
    var tab = $('.tblist')[0];
    var trs = $(tab).find('tr');
    var ths = $(tab).find('tr:first th');
    if(cno<0) cno = ths.length+cno;
    if(flag){
        $(ths).eq(cno).append(flag+title);
    }else{
        $(ths).eq(cno).html(title);
    }
    for(var i=1;i<trs.length;i++){
        var id = $(trs[i]).find('input:first').prop('name');
        id = id.replace('fs[','').replace(']',''); //jsLog(id);
        var val = tpls.replace('{id}',id);
        if(flag){
            $(trs[i]).find('td').eq(cno).append(flag+val);
        }else{
            $(trs[i]).find('td').eq(cno).html(val);
        }
    }
} 

function stsetLink(e){
    var url = $(e).prop('href'); 
    //var type = $("input[name='mtype']:checked").val();
    var limit = $("input[name='limit']").val();
    var offset = $("input[name='offset']").val();
    //offset = offset.length==0 ? 0 : offset;
    url = url+'&limit='+limit+'&offset='+offset+'';
    return winOpen(url,e.innerHTML);
}

function impmset(efm){
    if(!impstr) return;
    var pms = '(,'+impstr+',';
    $("input[name='fm[prmcb][]']").each(function(i, el) {
        var v = $(el).val();
        if(pms.indexOf(v)>0){
            $(el).attr('checked','true').attr('disabled','true');
        }
    }); 
}
