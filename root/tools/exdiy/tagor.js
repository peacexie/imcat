
var cfgs = {rtag:0, allp:0, allt:0, nulltpl:'(请设置`标签内模板代码`)'};
var defs = {tagtype_v:'v', show:'0', join:'0', limit:'10', offset:'0', cache:'0'};
// delete person.name;//删除属性
// cbk = JSON.parse(JSON.stringify(cfgs)),

// ----------------------------------------- 

// 属性->代码
function p2cAll(){
    var name = strTrim('#tagname'),
        res = '{tag:'+name+'=',
        tpl = strTrim('#-tpl_str-');
    res += p2cTagtype();
    res += p2cModid();
    res += p2cStype();
    res += p2cWhere();
    res += p2cLoc3();
    res += '}\n' + (tpl?tpl:cfgs.nulltpl) + '\n';
    res += '{/tag:'+name+'}';
    return res;
}
// 属性->代码:tagtype
function p2cTagtype(){
    var tp = strTrim('#tagtype'), tv = strTrim('#tagtype_v');
    tv = (tv!='v' && isKey(tv)) ? ','+tv : '';
    return '['+tp+tv+']';
}
// 属性->代码:modid
function p2cModid(){
    var tp=$('#tagtype').val(), dbkey='', join='', modid, pgbar='';
    if(tp=='Free'){
        dbkey = strTrim('#dbkey');
        dbkey = isKey(dbkey) ? '[dbkey,'+dbkey+']' : '';
        modid = strTrim('#modid_2');
        pgbar = $('#pgbar').prop('checked');
        pgbar = pgbar ? '[pgbar,1]' : '';
    }else{
        modid = strTrim('#modid');
        join = $('#join').prop('checked');
        join = join ? '[join,detail]' : '';
    }
    return dbkey+'[modid,'+modid+']'+pgbar+join;
}
// 属性->代码:stype/show
function p2cStype(){
    var res='', stype='', show='';
    if(!hasHidcls('stype')){
        stype = $('#stype').val();
        if(stype) res += '[stype,'+stype+']';
    }
    if(!hasHidcls('show_def')){
        show = $("input[name='show']:checked").val(); 
        if(show!='1') res += '[show,'+show+']';
    }
    return res;
}
// 属性->代码:pid/inids/.../where/order
function p2cWhere(){
    var tab = 'pid/inids/keywd/idfix/-ex_fields-/where/order',
        arr = tab.split('/'), res='', key='', val='', i;
    for(i=0; i<arr.length; i++){
        key = arr[i];
        if(!hasHidcls(key)){
            val = $('#'+key).val();
            if(val) res += key=='-ex_fields-' ? val : '['+key+','+val+']';
        }
    }
    return res;
}
// 属性->代码:limit/offset/cache
function p2cLoc3(){
    var tab = 'limit/offset/cache',
        arr = tab.split('/'), res='', key='', val='', i;
    for(i=0; i<arr.length; i++){
        key = arr[i];
        if(!hasHidcls(key)){
            val = $('#'+key).val();
            if(val!='0') res += '['+key+','+val+']';
        }
    }
    return res;
}

// ----------------------------------------- 

// 代码->属性
function c2pAll(code){
    var reg = /\{tag\:(\w+)\=\[([^\n]+)\]\}/i,
        org = code.match(reg); // 原始数组
    if(!org){ alert('Error Tag-Code!'); return 0; }
    var tagpre = org[0], tagfix = '{/tag:'+org[1]+'}',
        tab = JSON.parse(JSON.stringify(defs)), // 克隆默认值
        params = org[2].split(']['), 
        tagtype = params.shift(),
        tmp, i, p=0, k='', v='';
    if(tagtype.indexOf(',')>0){
        tmp = tagtype.split(',');
        tagtype = tmp[0];
        tab['tagtype_v'] = tmp[1];
    } 
    tab['tagname']=org[1], tab['tagtype']=tagtype;
    for(i=0;i<params.length;i++){
        tmp = params[i], p = tmp.indexOf(','),
        k = p>0 ? tmp.substring(0,p) : tmp,
        v = p>0 ? tmp.substring(p+1) : '';
        tab[k] = v;
    } //jsLog(tab);
    i = $.inArray(tagtype,cfgs.types), p = typeof(tab['modid'])=="undefined"; 
    if(i<0||p){ alert('Error Tag-Items!'); return 0; }
    c2pItms(tab);
    i = code.indexOf(tagpre)+tagpre.length, p = code.indexOf(tagfix),
    tmp = strTrim(code.substring(i,p));
    $('#-tpl_str-').val(tmp?tmp:cfgs.nulltpl);
    return 1;
}
function c2pItms(tab){
    var idom, exps='';
    $.each(tab, function(idx,val){
        idom = $('#-att_tab-').find('#'+idx);
        if(',show,join,pgbar'.indexOf(idx)>0){
            if(idx=='show'){
                if($.inArray(val,['0','all','1'])<0){
                    $('#show_'+val).prop('checked',true);
                } 
            }else{
                $(idom).prop('checked',val>'0');
            }
        }else if(',tagtype,modid,'.indexOf(idx)>0){
            if(idx=='modid'&&tab['tagtype']=='Free') idom = $('#modid_2');
            $(idom).val(val);
            idx=='tagtype' ? opTagtype(idom) : opModid(idom);
        }else if($(idom).length>0){ // hasDom
            $(idom).val(val);
        }else{ // -ex_fields-
            if(val.indexOf(']')){ val = val.substring(0,val.indexOf(']')); }
            if(!val || val.match(/^\$?\w+$/i,val)){
                val = val ? ','+val : ''; 
                exps += '['+idx+val+']';
            }
        }
    });
    if(exps){ $('#-ex_fields-').val(exps); }
}

// ----------------------------------------- 

// 切换:标签类型
function opTagtype(e){
    $('#modid').get(0).selectedIndex = 0; // 模型:默认第一个
    $('#stype').find("option").not(":first").remove(); // 栏目:清空
    var val = $(e).val();
    if(!val){
        initItems(); 
    }else{
        setItems('tps',val,'tphid');
        if(val=='Free'){ $('#-att_tab- .itag').removeClass('mdhid'); }
        $('#modid option').hide();
        $('#modid').find('option').eq(0).show();
        if(val=='Type' || val=='Push'){
            if(val=='Type'){ $('#modid option[pid="types"]').show(); }
            if(val=='Push'){ $('#modid option[pid="advs"]').show(); }
        }else{
            $('#modid option[pid="docs"]').show();
            $('#modid option[pid="users"]').show();
            $('#modid option[pid="coms"]').show();
        }
    }
}
// 切换:Modid模型
function opModid(e){
    var val = $(e).val();
    var pid = $(e).find("option:selected").attr('pid');
    if(!pid){
        setItems('mds','(nul)','mdhid');
        $('#btnCreate').prop('disabled',true);
        return;
    } 
    setItems('mds',pid,'mdhid');
    $('#btnCreate').prop('disabled',false);
    // ajax-stype
    if(val=='coms' || val=='types'){ return; }
    $('#stype').find("option").not(":first").remove();
    $.ajax({
        url: '?mod='+val,
        dataType: 'jsonp',
        success:function(res){
            $.each(res, function(i, itm){
                var kid=itm.kid, val=itm.title, deep=parseInt(itm.deep);
                if(deep>1){ for(var i=0;i<deep-1;i++){
                    val = "　"+val;
                } }
                $('#stype').append($("<option>",{value:kid,text:val+'['+kid+']'}));
            }); 
        },
        error:function(err){jsLog(err)}
    });
}
// 切换:Modid模型
function opModid_2(e){
    var dbkey = $('#dbkey').val();
    var modid_2 = $('#modid_2').val();
    if(dbkey && modid_2){
        $('#btnCreate').prop('disabled',false);
    }else{
        $('#btnCreate').prop('disabled',true);
    }
}

// init
function initAll(first){
    if(first){ 
        $("#-att_tab-").find("li.ir").prepend(tipmk);
        setTipc(); 
    } // return;
    $('#-att_tab- .tip').hide(); // hid-Tips
    initItems(); // hid-Itms
    $('#irestore').hide();
}
// 初始化显示项
function initItems(){
    $('#-att_tab- .itag').each(function(i,e){
        var tps = $(e).attr('tps');
        if(tps) $(e).addClass('tphid');
        var mds = $(e).attr('mds');
        if(mds) $(e).addClass('mdhid');
    });
    $('#btnCreate').prop('disabled',true);
}

// 设置Tip-click
function setTipc(){
    $('#-att_tab- .qmark').each(function(i,e){
        $(e).click(function(){
            var p = $(e).parent().parent();
            var tip = $(p).find('.tip');
            $(tip).toggle(); // .trigger()
        });
    });  
}
// 设置显示项
function setItems(key, val, cls){
    $('#-att_tab- .itag').each(function(i,e){
        var vals = $(e).attr(key);
        if(!vals) return;
        if(vals.indexOf(val)>=0){
            $(e).removeClass(cls);
        }else{
            $(e).addClass(cls);
        } 
    });   
}

// ----------------------------------------- 

// 含有:tphid,mdhid至少一个class
function hasHidcls(key){
    var p = $('#'+key).parent().parent(),
        cls = $(p).prop('class'),
        ind = cls.indexOf('hid'); //jsLog(cls,ind);
    return ind>0;  
}
function isKey(key){
    var patt = /^[a-zA-Z]\w+$/;
    return patt.test(key);  
}
function isParam(key){
    var patt = /^(\$\w+)|([\w\-]+)$/;
    return patt.test(key);  
}
function strTrim(key, fmt){
    var val, patt = /^\#[\w-]+$/;
    if(patt.test(key)){
        val = $(key).val();
    }else{
        val = key;
    }
    return val.replace(/(^\s*)|(\s*$)/g, '');
} 

// ----------------------------------------- 

// 生成代码 
function tagCreate(){
    //$('#-rec_box-').val('code');
    $('#-rec_box-').show();
    $('#itpl').show();
    var code = p2cAll();
    $('#-tag_str-').val(code);
}
// 还原属性 
function tagRestore(){
    var code = $('#-tag_str-').val(),
        res = c2pAll(code);
    if(res){
        $('#-att_tab-').show();
        $('#itpl').show();   
    }   
}
// 重新配置属性
function tagResetp(){
    tagRestore();
    $('#icreate').show();
    $('#irestore').hide();
    $('#-rec_box-').hide();
    cfgs.rtag = 0;
}

// 切换:标签还原
function opFlip2(){
    $('#icreate,#irestore,#-att_tab-,#-rec_box-').hide();
    if(cfgs.rtag){
        $('#-att_tab-').show();
        $('#icreate').show();
        cfgs.rtag = 0;
    }else{
        $('#-rec_box-').show();
        $('#irestore').show();
        cfgs.rtag = 1;
    }
}
// 切换:所有属性
function opAllp(){
    if(cfgs.allp){
        $('#tagtype').get(0).selectedIndex = 0; // 标签:默认第一个
        $('#modid').get(0).selectedIndex = 0; // 模型:默认第一个
        $('#stype').find("option").not(":first").remove(); // 栏目:清空
        initAll();
        cfgs.allp = 0;
    }else{
        $('#-att_tab- .itag').removeClass('tphid');
        $('#-att_tab- .itag').removeClass('mdhid');
        cfgs.allp = 1;
    }
}
// 切换:所有提示
function opAllt(){
    if(cfgs.allt){
        $('#-att_tab- .tip').hide();
        cfgs.allt = 0;
    }else{
        $('#-att_tab- .tip').show();
        cfgs.allt = 1;
    }
}   

