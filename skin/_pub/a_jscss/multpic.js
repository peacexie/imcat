
// 图片组操作

var mpic_cfgs = {}; 

//初始化
function mpic_minit(fmid){
    var cfg,i,a;
    cfg = mpic_data(fmid, 1); 
    $('#'+fmid+'show').html('');
    str = ''; 
    for(i=0;i<cfg.data.length;i++){
        a = cfg.data[i].split(',');
        mpic_mshow(fmid,a[0], (a.length>0 ? a[1] : ''));
        str += cfg.data[i]+';\n';
    } 
    mpic_marea(fmid,str);
}
//显示一个图(管理)
function mpic_mshow(fmid,file,val){
    var cfg,sets,pic;
    cfg = mpic_data(fmid); 
    opt = cfg.opt; 
    if(val){ 
        opt = opt.replace("value='"+val+"'","value='"+val+"' selected");    
    }
    sets = "<i class='close' title='"+lang('jcore.mpic_del')+"' onclick=\"mpic_mdel('"+fmid+"',this)\">[X]</i>"+opt;
    pic = "<div class='pitem'>"+sets+"<img src='"+file+"' width=120 height=90 onload='imgShow(this,120,90)'></div>";
    $('#'+fmid+'show').append(pic);
}
//增加一个图
function mpic_madd(fmid,file){
    var cfg, k, i, a, val;
    cfg = mpic_data(fmid); k = 0; 
    for(i=0;i<cfg.data.length;i++){
        a = cfg.data[i].split(',');
        if(a[0]==file){
            k++; break;
        }
    }
    if(!k){
        mpic_mshow(fmid,file,val);
        val = $('#'+fmid).val();
        mpic_marea(fmid,val+file+';\n');
        cfg.data.push(file);
    }else{    
        alert(lang('jcore.mpic_readd')); 
    }
}
//删除一个图
function mpic_mdel(fmid,e){
    var itm, url, cfg, str, i, a, d;
    itm = $(e).parent(); 
    url = $(itm).find('img').attr('src'); 
    $(itm).remove(); 
    cfg = mpic_data(fmid); 
    str = ''; d=new Array();
    for(i=0;i<cfg.data.length;i++){
        a = cfg.data[i].split(',');
        if(a[0].length>0 && a[0].indexOf(url)>=0){
            continue;
        }else{
            d.push(cfg.data[i]);
            str += cfg.data[i]+';\n';
        }
    }
    cfg.data = d;
    mpic_marea(fmid,str);
}
//设置一个图
function mpic_mset(fmid,e){
    var val,itm,url,cfg,str,d,i,a;
    val = $(e).val(); 
    itm = $(e).parent(); 
    url = $(itm).find('img').attr('src');
    val = url+','+val;
    cfg = mpic_data(fmid); 
    str = ''; d=new Array();
    for(i=0;i<cfg.data.length;i++){
        a = cfg.data[i].split(',');
        if(a[0].length>0 && a[0]==url){
            cfg.data[i] = val;
        }
        d.push(cfg.data[i]);
        str += cfg.data[i]+';\n';
    }
    cfg.data = d;
    mpic_marea(fmid,str);
}
//重设area的值
function mpic_marea(fmid,val){
    $('#'+fmid).val(val);
    $('#'+fmid).html(val);
}
//清除area的值
function mpic_clear(fmid){
    mpic_marea(fmid,''); 
    $('#'+fmid+'show').html('');
    cfg = mpic_data(fmid);
    cfg.data = new Array();
}

//显示-初始化
function mpic_view(fmid,w,h,play){
    var cfg,i,a,v,pic,opt,str='';
    cfg = mpic_data(fmid, 1);
    for(i=0;i<cfg.data.length;i++){
        a = cfg.data[i].split(',');
        v = a.length>0 ? a[1] : '';
        img = "<img src='"+a[0]+"' width="+w+" height="+h+" data-val='"+v+"' onload='imgShow(this,"+w+","+h+")'>";
        pic = "<div class='pview'>"+(play?"<a href='"+a[0]+"' title='' rel='viewPhoto["+fmid+"]'>":'')+img+(play?"":'</a>')+"</div>";
        str += pic;
    }
    $('#'+fmid+'show').html(str);
    opt = cfg.opt.replace(lang('jcore.mpic_select'),lang('jcore.mpic_allitems')).replace("mpic_mset(","mpic_vtype(");
    $('#'+fmid+'out .seltype').html(opt);
    $('#'+fmid+'out .cntall').html(cfg.data.length);
}
//显示-按类别
function mpic_vtype(fmid,e){ 
    var val, n=0;
    val = $(e).val(); 
    if(val){ 
        $('#'+fmid+'show .pview').each(function(index, element) {
            if($(this).find('img:first').attr('data-val')==val){ 
                $(this).show();
                n++;
            }else{ 
                $(this).hide();
            }
        });
    }
    if(!n){
        if(val){ alert(lang('jcore.mpic_norelate')); $(e).val(''); }
        $('#'+fmid+'show .pview').show();
        n = $('#'+fmid+'show .pview').length;
    }
    $('#'+fmid+'out .cntall').html(n);
}

//初始数据
function mpic_data(fmid, rst){
    var cfg, arr, opt, str, i, re={}, c=new Array(), d=new Array();
    if(rst){
        var cfg = $('#'+fmid+'show').html();
        var str = $('#'+fmid).val();
        eval("mpic_cfgs."+fmid+" = {}; mpic_cfgs."+fmid+".cfg = cfg;");
    }
    eval("re = mpic_cfgs."+fmid+";"); 
    if(rst){
        cfg = cfg.replace(/\n/g,'').replace(/\r/g,'').replace(/ /g,'').split(';');
        arr = str.replace(/\n/g,'').replace(/\r/g,'').replace(/ /g,'').split(';'); 
        opt = "<option value=''>"+lang('jcore.mpic_select')+"</option>"; 
        for(i=0;i<cfg.length;i++){
            if(cfg[i].indexOf('|')>0){ 
                c.push(cfg[i]); a = cfg[i].split('|');
                opt += "<option value='"+a[0]+"'>"+a[1]+"</option>";
            }
        }
        for(i=0;i<arr.length;i++){
            if(arr[i].length>12) d.push(arr[i]);
        }
        re.opt = "<select onchange=\"mpic_mset('"+fmid+"',this)\">"+opt+"</select>"; 
        re.data=d; re.cfg = c; 
    }
    return re;
}

/*

*/
