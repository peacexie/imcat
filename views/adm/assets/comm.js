
var admSloc = new mulStore('local');
var admSses = new mulStore('session');

function admLinks(){
    var _o = $('#adi_links p');
    if($(_o).css('display')==='none'){
        $(_o).show(300);
    }else{
        $(_o).hide(300);
    }
    $('#adi_links i').toggleClass('fa-caret-square-o-up').toggleClass('fa-caret-square-o-down');
}

// display-显示
function admNavd(){
    var last = admSloc.get('abcNav_last'),
        tabs = admSloc.get('abcNav_tabs'),
        res = new Array();
    if(tabs){
        try{ res = JSON.parse(tabs); }
        catch(ex1){ jsLog(ex1); }
    }
    try{ last = JSON.parse(last); }
    catch(ex2){ jsLog(ex2); }
    if(last) res.unshift(last); 
    // adi_links
    var html='<b>'+Lang.adm.nav_nearest+'</b>', 
        data='', rows=[], drow='', cnt=0;
    $.each(res,function(id,el){
        drow = JSON.stringify(el);
        if(data.indexOf(drow)<0){
            var p = " onclick='admLnkc(this)' p='"+drow+"' ";
            html += "<a href='"+el.url+"' "+p+">"+el.pt+" &gt; "+el.title+"</a>";
            data += (id>0?',\n':'')+drow;
            rows.push(el);
            cnt++; if(cnt==10){ return false; }
        }
    });
    $('#adi_links p').html(html);
    // adi_navbc
    var el = res[0],
        lnk1 = "<li><a onclick=\"admNavc('"+el.rid+"')\">"+el.rt+"</a></li>", 
        lnk2 = "<li><b>"+el.pt+"</b></li><li><b href='"+el.url+"'>"+el.title+"</b></li>";
    $('#adi_navbc').append(lnk1+lnk2);
    // save-configs
    //data = '['+data+']';
    data = JSON.stringify(rows);
    admSloc.set('abcNav_tabs', data);
}
// click-adi_navbc
function admLnkc(e){
    var drow = $(e).attr('p');
    try{ last = JSON.parse(drow); }
    catch(ex2){ jsLog(ex2); }
    admSloc.set('abcNav_last', drow);
    window.parent.admSetTab(last.rid);
    location.href = $(e).prop('href');
    return false;
}
// click-adi_links
function admNavc(rid){
    if(top.location!=self.location)
    window.parent.admSetTab(rid);
}

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

// var tpl = "<a href='?dops-a&mod=topic&did={id}&view=cfgs' target='_blank'>资料管理</a>";
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

function altDbpre(url){ // 修改表前缀(`db_act.php`使用)
    var pre1 = $('#pre1').val(), pre2 = $('#pre2').val(); 
    url += '&pre1='+pre1+'&pre2='+pre2+''; 
    return winOpen(url,'修改表前缀');
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
