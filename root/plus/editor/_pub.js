
function edt_showBar(fid){
    var bar = '', mod = edt_sysMod, kid = edt_sysKid;
    //bar += "<a onClick=\"winOpen('"+_cbase.run.roots+"/plus/editor/em.htm?fid="+fid+"','插入常用表情图',600,420)\" title=\"插入常用表情图\n(7套方案)\">表情</a>";
    bar += "<a onClick=\"winOpen('"+_cbase.run.roots+"/plus/editor/tpl_char.php?fid="+fid+"&pSub=baidu','"+lang('jedt.inschar')+"',720,560)\" title=\""+lang('jedt.inschar')+"\n("+lang('jedt.ncase',3)+")\">"+lang('jedt.char')+"</a>";
    bar += "<a onClick=\"winOpen('"+_cbase.run.roots+"/plus/editor/tpl_doc.php?fid="+fid+"&pSub=common','"+lang('jedt.instpl')+"',720,560)\" title=\""+lang('jedt.instpl')+"\n("+lang('jedt.ncase',2)+")\">"+lang('jedt.tpl')+"</a>";
    bar += "<a onClick=\"winOpen('"+_cbase.run.roots+"/plus/file/fview.php?fid="+fid+"&mod="+mod+"&kid="+kid+"','"+lang('jedt.fadmin')+"',720,560)\" title=\""+lang('jedt.fadmin')+"\n("+lang('jedt.fpick',2)+")\">"+lang('jedt.file')+"</a>";
    bar += "<a onClick=\"winOpen('"+_cbase.run.roots+"/plus/file/media.php?fid="+fid+"&mod="+mod+"&kid="+kid+"','"+lang('jedt.insmedia')+"',720,560)\" title=\""+lang('jedt.insmedia')+"\n("+lang('jedt.mtitle',2)+")\">"+lang('jedt.media')+"</a>";
    bar += "<a onClick=\"edt_InsPage('"+fid+"')\" title=\""+lang('jedt.inspage')+"\n("+lang('jedt.ptitle')+")\">"+lang('jedt.page')+"</a>";
    bar += "<a onClick=\"edt_InsDate('"+fid+"')\" title=\""+lang('jedt.insdate')+"\n(eg:2013-12-31)\">"+lang('jedt.date')+"</a>";
    bar += "<a onClick=\"edt_InsTime('"+fid+"')\" title=\""+lang('jedt.instime')+"\n(eg:23:12:30)\">"+lang('jedt.time')+"</a>";
    //bar += "<a onClick=\"winOpen('"+_cbase.run.roots+"/plus/mfunc/dialog.htm?fid="+fid+"','插入数学公式',720,560)\" title=\"插入数学公式\">公式</a>";
    var e = jsElm.jeID(fid+'bar');
    if(e) e.innerHTML = bar;
    return bar;
}

function edt_InsPage(fid,title){
    var html = "<p><hr class='split_pager'></p>"; 
    edt_Insert(fid, html);
}
function edt_InsDate(fid){
      var date = new Date();
      var nYear   = date.getFullYear(); 
      var nMonth  = date.getMonth()+1; if(nMonth<10) nMonth = '0'+nMonth;
      var nDay    = date.getDate();    if(nDay<10) nDay = '0'+nDay;  
    var html = ' ' + nYear + '-' + nMonth + '-' + nDay + ' ';
    edt_InsText(fid, html);
}
function edt_InsTime(fid){
      var date = new Date();
      var nHour   = date.getHours();   if(nHour<10) nHour = '0'+nHour;
      var nMinute = date.getMinutes(); if(nMinute<10) nMinute = '0'+nMinute;
      var nSecond = date.getSeconds(); if(nSecond<10) nSecond = '0'+nSecond;
    var html = ' ' + nHour + ':' + nMinute + ':' + nSecond + ' ';
    edt_InsText(fid, html);
}

function edt_InsImage(fid,url,title,w,h){
    var html = "<img src='"+url+"' ";
    if(title && title.length>0) html += " alt='"+title+"' ";
    if(w) html += " width='"+w+"' ";
    if(h) html += " height='"+h+"' ";
    html += " />"; 
    edt_Insert(fid, html);
}

// type=iframe,map,swf,audio,video
// val=url/map, cfg=w,h,align
function edt_InsMedia(fid,type,val,cfg){
    var str = "{media:";
    str += "[type="+type+"][val="+encodeURIComponent(val)+"]";
    str += (cfg.w ? "[w="+cfg.w+"]" : '');
    str += (cfg.h ? "[h="+cfg.h+"]" : '');
    str += (cfg.a ? "[a="+cfg.a+"]" : '');
    str += (cfg.ext ? "[ext="+cfg.ext+"]" : '');
    str += "/media}";
    str = str.replace('[w=]','').replace('[h=]','').replace('[a=]','');
    edt_Insert(fid, str);
} 

