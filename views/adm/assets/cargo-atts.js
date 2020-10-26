

var fmPartHtml = '';

function partLoad(first){
    if(first){
        fmPartHtml = $('#fmPartOut').html();
        $('#fmPartOut').html('');
    }else{
        $('#partLists .uwrap').remove();
    }
    var did = $('input[id="fm[did]"]').val();
    var url = '?exinc-parts&pid='+did+'&_r'+Math.random();
    $.ajax({
        url: url,
        contentType: "application/json; charset=utf-8",
        success: function(data) { //jsLog(data.gtab);
            var html='', arr=data.arr; 
            if(arr && arr.length){
                $.each(arr, function(i, row){
                    var str = '<tr class="tc uwrap">';
                        str += '<td class="hidden">'+row['kid']+'</td>';
                        str += '<td>'+row['title']+'</td>';
                        str += '<td>'+row['guige']+'</td>';
                        str += '<td>'+row['price']+'</td>';
                        str += '<td>'+row['top']+'</td>';
                        str += '<td>'+row['cnt']+'</td>';
                        str += '<td><a onClick="partEdit(\''+row['kid']+'\')">改</a> # <a onClick="partDel(\''+row['kid']+'\')">删</a></td>';
                    html += str;
                });
                $("#part_acts").before(html);
            }else{
                $("#part_acts").before("<tr class='tc uwrap'><td class=''>暂无配件</td></tr>");
            }
        },
        error: function (err) { 
            jsLog(err);
        }
    }) 
}
function partOpen(){
    layer.open({
        type: 1, title:'配件详情', 
        skin: 'layui-layer-rim', //加上边框
        area: ['420px', '380px'], //宽高
        content: fmPartHtml,
        scrollbar: true
    });
}
function partSave(){
    var inps = $('#fmPart').find('input'), 
        data = {}; 
    for(var i=0;i<inps.length-1;i++){
        var itm = inps[i],
            key = itm.id.replace('pt_',''),
            val = $(itm).val();
        data[key] = val;
    }
    data['attcom'] = $('#pt_attcom').val();
    //jsLog(data);
    var pid = $('input[id="fm[did]"]').val();
    var url = '?exinc-parts&act=save&kid='+data['kid']+'&pid='+pid+''; 
    $.ajax({
        url: url, type: 'POST', data:data,
        success: function(data) { //jsLog(data.gtab);
            var cnt = data.cnt; 
            if(cnt && cnt>=1){
                alert(data.res);
            }else{
                $('.layui-layer-close').trigger('click');
                partLoad();
            }
        },
        error: function (err) { 
            jsLog(err);
        }
    }) 
}
function partEdit(kid){ 
    var url = '?exinc-parts&act=find&kid='+kid+'';
    $.ajax({
        url: url,
        contentType: "application/json; charset=utf-8",
        success: function(data) { //jsLog(data.gtab);
            var html='', row=data.row; 
            if(row){
                partOpen();
                $.each(row, function(key, val) {
                    //jsLog(key+':'+val)
                    $('#pt_'+key).val(val);
                });
            }else{
                //$("#part_acts").before("<tr class='tc uwrap'><td class=''>x</td></tr>");
            }
        },
        error: function (err) { 
            jsLog(err);
        }
    }) 
}
function partDel(kid){ 
    var url = '?exinc-parts&act=del&kid='+kid+''; // _cbase.run.fbase + 
    $.ajax({
        url: url,
        contentType: "application/json; charset=utf-8",
        success: function(data) { //jsLog(data.gtab);
            var html='', res=data.res; 
            if(res){
                alert('OK');
            }else{
                alert('NG');
            }
            partLoad()
        },
        error: function (err) { 
            jsLog(err);
        }
    }) 
}

function partPick(e){
    url = _cbase.run.fbase+'?ajax-parts&mod=cargo';
    popOpen(lang('jcore.pop_infopick'),url,640,320);
}


function laycb_cargo_catid(mod, key, pid) {
    var url = _cbase.run.fbase + '?ajax-types&mod=rels&rid=relpb&pid='+pid+'&up=brand'; // _cbase.run.fbase + 
    $.ajax({
        url: url,
        contentType: "application/json; charset=utf-8",
        success: function(data) { 
            rel_cbids = data.arr.rids; 
            laycb_brand_brand(1, 2, 3);
        },
        error: function (err) { 
            jsLog(err);
        }
    }) 
}
function laycb_brand_brand(mod, key, pid) { 
    for(let i=1;i<3;i++){
        $("#lt_brand_"+i+' option').each(function(id,e){
            let ival = $(e).val(); 
            if(ival && rel_cbids.indexOf(ival)<0){
                $(e).attr('disabled',true);
            }else{
                $(e).attr('disabled',false);
            }
        });
    }
}

function laycb_umod_attmod(mod, key, pid) {
    $('#umod_atts').html('');
    if(!pid || pid=='undefined'){
        return; 
    }
    $("textarea[name='fm[attso]']").val('');
    $("textarea[name='fm[attcom]']").val('');
    var url = _cbase.run.fbase + '?ajax-types&mod=uatt&pid='+pid+'';
    $.ajax({
        url: url,
        contentType: "application/json; charset=utf-8",
        success: function(data) { //jsLog(data.gtab);
            var html='', htab={}, tab2={}, arr=data.arr; 
            tab2['com'] = ',', tab2['so'] = ','; 
            if(arr && arr.length){
                if(data.gtab.length>1){
                    $.each(data.gtab, function(gi, gkey) {
                        htab[gkey] = attTitle(gkey); 
                    });
                    $.each(arr, function(i, row){
                        if(row.gkey && row.gkey in htab){
                            //
                        }else{
                            row.gkey = '(未分组)';
                            if(!htab['(未分组)']){
                                data.gtab.push('(未分组)');
                                htab['(未分组)'] = attTitle('(未分组)');
                            }
                        }
                        gkey = row.gkey;
                        htab[gkey] += att1Html(row);
                        var tkey = row.so=='1' ? 'so' : 'com';
                        tab2[tkey] += row.title+',';
                    });
                    $.each(data.gtab, function(gi, gkey) {
                        $('#umod_atts').append(htab[gkey]);
                    });
                }else{
                    $.each(arr, function(i, row){
                        html += att1Html(row);
                        var tkey = row.so=='1' ? 'so' : 'com';
                        tab2[tkey] += row.title+',';
                    });
                    $('#umod_atts').append(html);
                }
                $('#tab_com').val(tab2['com']);
                $('#tab_so').val(tab2['so']);
                attSetVals();
            }else{
                //$(elm).hide();
            }
        },
        error: function (err) { 
            jsLog(err);
        }
    })    
}

function attSetVals(){
    var s_com = $('#tab_com').val(),
        s_so = $('#tab_so').val(),
        tab = (s_com+','+s_so).split(','), 
        res = {'com':'', 'so':''}; //jsLog(v1); jsLog(v2);
    for(var i=0;i<tab.length;i++){
        if(!tab[i]){ continue; }
        var ikey = s_so.indexOf(','+tab[i]+',')>=0 ? 'so' : 'com';
        var rcb = $("input[name='att["+tab[i]+"]']:checked"),
            sel = $("select[name='att["+tab[i]+"]']"),
            txt = $("textarea[name='att["+tab[i]+"]']"),
            str = $("input[name='att["+tab[i]+"]']").val();
        if(rcb.length>0){ // 
            var itms = ',';
            for(var j=0;j<rcb.length;j++) {
                if(!$(rcb[j]).val()){ continue; }
                itms += $(rcb[j]).val()+',';
            }
            res[ikey] += tab[i]+'=`'+itms+'`\n';
        }else if(sel.length>0){
            res[ikey] += tab[i]+'=`'+$(sel).val()+'`\n';
        }else if(txt.length>0){ // text
            res[ikey] += tab[i]+'=`'+$(txt).val()+'`\n';
        }else{ // input
            res[ikey] += tab[i]+'=`'+(str?str:'')+'`\n';
        }
    } //jsLog(res);
    $("textarea[name='fm[attso]']").val(res['so']);
    $("textarea[name='fm[attcom]']").val(res['com']);
    return res;
}

function att1Html(cfg){
    var kid = cfg.title, tab = [], no = 0; // onblur='attSetVals()' 
        fnameid = " name='att["+kid+"]' id='att["+kid+"]' onchange='attSetVals()' ";
        html = "<tr ><td class='tc'>"+cfg.title+"</td><td class='tl'>",
        val = kid in att_vals ? att_vals[kid] : ''; //jsLog(att_vals[kid]);
    if(cfg.cfgs){
        var tmp = cfg.cfgs.replace('\r','\n').split('\n'); 
        for(var i=0;i<tmp.length;i++){
            var itm = tmp[i].replace(/\s+/g,'');
            if(!itm){ continue; }
            tab[no] = itm; no++; 
        }
    }
    if(cfg.type=='select'){
        html += "<select "+fnameid+">";
        html += "<option value='' >-选择-</option>";
        for(var i=0;i<tab.length;i++){
            var sed = val.indexOf(tab[i])>=0 ? 'selected' : '';
            html += "<option "+sed+">"+tab[i]+"</option>";
        }
        html += "</select>";
    }else if(cfg.type=='cbox'){
        html += "<input "+fnameid+"0 type='hidden' value='' />";
        for(var i=0;i<tab.length;i++){
            var sed = val.indexOf(tab[i])>=0 ? 'checked' : '';
            html += "<label><input "+fnameid+i+" type='checkbox' class='rdcb' value='"+tab[i]+"' "+sed+">"+tab[i]+"</label> ";
        }
    }else if(cfg.type=='radio'){
        html += "<input "+fnameid+"0 type='hidden' value='' />";
        for(var i=0;i<tab.length;i++){
            var sed = val.indexOf(tab[i])>=0 ? 'checked' : '';
            html += "<label><input "+fnameid+i+" type='radio' class='rdcb' value='"+tab[i]+"' "+sed+">"+tab[i]+"</label> ";
        }
    }else if(cfg.type=='text'){
        html += "<textarea "+fnameid+" rows='3' class='txt' >"+val+"</textarea>";
    }else{
        html += "<input "+fnameid+" type='text' value='"+val+"' class='txt' />";
    }
    html += "</td></tr>";
    return html;
}
function attTitle(gkey){
    html = "<tr><td class='tc cCCC'>---</td><td class='tl'><b>"+gkey+"</b></tr>";
    return html;
}

var rel_cbids='(null)', types_inited = 0;

$(function(){
    $('#sec_attr').find('select').prop("disabled","disabled");
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        //var url = $(e.target).prop('href');
        if(!types_inited){
            $('#sec_attr').find('select').removeAttr("disabled");
            layInit('umod','attmod');
            layInit('cargo','catid');
            layInit('brand','brand');
            types_inited = 1;
        } //jsLog(e.target); // 属性参数/配件关联
        //e.relatedTarget   // 上一次活动的标签页
    })
    var li3 = $('.nav-tabs').find('li').eq(0).find('a');
    $(li3).trigger("click"); //jsLog(li3);
    // 
    partLoad(1);
})


/*
var tab = 'attso,attcom,attdel'.split(',');
for(var i=0;i<tab.length;i++){
    $("textarea[id='fm["+tab[i]+"]']").parent().parent().hide();
}*/
