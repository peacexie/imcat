
// 搜索排序工具条(search,order,bar)
// prec=14&ptype=next&pkey=

var sordb_cfgs = {}; // a=0; b='0'; c=''; -=> f,t,f

// 初始化
// sordb_init('{=$this->mod}','{=$this->key}',0,'stype:brand,price;typ2:fff1,fff2',"<a href='(url)' class='(act)'>(title)</a>"); 
function sordb_init(mod,key,url,rel,tpl){ 
    var unow, stype, org, v;
    unow = window.location.href;
    stype = urlPara('stype','',unow);
    sordb_cfgs.mod = mod; 
    sordb_cfgs.stype = stype ? stype : (key ? key : ''); 
    sordb_cfgs.slay = sordb_typeLay(mod,sordb_cfgs.stype);
    if(url){
        org = url
    }else if(unow.indexOf('?')>0){ //搜索页
        org = unow; 
    }else{ //静态, ?cargo-type
        org = _cbase.run.csname+'?'+sordb_cfgs.mod+(sordb_cfgs.stype>0 ? '&stye='+sordb_cfgs.stype : '');
        if(sordb_cfgs.stype && !stype) org += "&stype="+sordb_cfgs.stype;
    }
    if(org.indexOf('?')<=0) org += '?';
    v = sordb_mftUrl(org,'order'); v = sordb_mftUrl(v,'odesc'); v = sordb_mftUrl(v,'page');
    v = sordb_mftUrl(v,'pkey'); v = sordb_mftUrl(v,'ptype'); v = sordb_mftUrl(v,'prec');
    sordb_cfgs.now = org; 
    sordb_cfgs.so = v;
    sordb_cfgs.page = sordb_mftUrl(org,'page'); 
    sordb_cfgs.rel = rel ? rel : '';
    sordb_cfgs.tpl = tpl ? tpl : "<a href='(url)' class='(act)'>(title)</a>"; 
}

// 搜索链接:栏目/等级
// sordb_sotype(linktpl,pid,'act','>=Clear'); pid=0,c1024等
function sordb_sotype(tpl,pid,act,clear){
    var key, dval, dlay, data;
    key = 'stype'; pid = pid ? pid : '0';
    dval = sordb_cfgs.stype; 
    dlay = sordb_typeLay(sordb_cfgs.mod,dval); 
    data = sordb_data(sordb_cfgs.mod+'-'+pid); 
    return sordb_links(tpl,key,act,clear,data,dlay);
}
// 搜索链接:扩展类别
// sordb_extype(0,'china-pid','act','-'); pid=0,gd等
function sordb_extype(tpl,keyp,act,clear){
    var a, key, dval, dlay, data;
    a = keyp.split('-'); key = a[0]; pid = a.length>0 ? a[1] : '0';
    dval = urlPara(key,''); 
    dlay = sordb_typeLay(key,dval); 
    data = sordb_data(keyp+'-'+pid); 
    return sordb_links(tpl,key,act,clear,data,dlay);
}
// 搜索链接:类别关联
// sordb_relat(linktpl,'brand-relpb-pid','act','>=(全部)'); pid=(空),c012等
function sordb_relat(tpl,keyp,act,clear){
    var a, key, dval, dlay, data;
    a = keyp.split('-'); key = a[0]; rel = a.length>0 ? a[1] : ''; pid = a.length>1 ? a[2] : '';
    dval = urlPara(key,''); 
    dlay = sordb_typeLay(key,dval); 
    data = sordb_drel(key,rel,pid);
    return sordb_links(tpl,key,act,clear,data,dlay);
    // _relpb_data
}
// 搜索链接:字段,扩展字段
// sordb_field(linktpl,'exp_s01','act','<=[Clear]');
function sordb_field(tpl,key,act,clear){
    var dval, data;
    dval = urlPara(key,''); 
    data = sordb_data(key); 
    return sordb_links(tpl,key,act,clear,data,dval);    
}
// 搜索链接:字段,所有扩展字段
// sordb_fexts('sobar',0,'act');
function sordb_fexts(barid,tpl,act,clear,tpl2){
    var key,keys,k,aprops,itm,str = '';
    if(!tpl2) tpl2 = '<p><b>(title)</b><span>(items)</span></p>';
    keys = sordb_exFields();
    if(keys.length>0){
        eval('aprops = _'+sordb_cfgs.mod+'_fields.'+sordb_cfgs.stype);
        for(k=0;k<keys.length;k++){
            itm = sordb_field(tpl,keys[k],act,clear); 
            if(itm){ 
                str += tpl2.replace('(key)',keys[k]).replace('(title)',aprops[keys[k]].title).replace('(items)',itm);
            }    
        }
    }
    $("#"+barid).append(str);    
}
// 搜索链接:数字区间
// sordb_area(linktpl,'price:10,100,200,300,500,800,1000:元','act','>=[Clear]');
function sordb_area(tpl,keyc,act,clear){
    var a, key, unt, dval, data;
    a = keyc.split(':'); key = a[0]; unt = a.length>1 ? a[2] : lang('jcore.sobar_curunit');
    dval = urlPara(key,''); 
    data = sordb_darea(a[1],unt); 
    return sordb_links(tpl,key,act,clear,data,dval);
}
// 搜索链接:公用
function sordb_links(tpl,key,act,clear,data,vals){
    var re, burl, i, icfg, iurl, sact, itmp;
    if(!tpl) tpl = sordb_cfgs.tpl;
    burl = sordb_mftUrl(sordb_cfgs.so, key);
    re = ''; vals = '(,'+vals+',)'; 
    for(i=0;i<data.length;i++){
        icfg = data[i].split('=');
        if(!icfg[0] || !icfg[1]) continue; // !undefined
        iurl = burl + '&' + key + '=' + icfg[0];
        iurl = sordb_mftUrl(iurl);
        sact = vals.indexOf(','+icfg[0]+',')>0 ? act : '';
        itmp = tpl.replace('(url)',iurl).replace('(title)',icfg[1]).replace('(act)',sact);
        re += itmp+"\n";
    }
    clear = clear ? (clear=='-' ? '' : clear) : '>='+lang('jcore.sobar_all');
    if(re && clear){ 
        icfg = clear.split('='); 
        sact = vals.replace('(null)','').length==4 ? act : ''; 
        itmp = tpl.replace('(url)',burl).replace('(title)',icfg[1]).replace('(act)',sact);
        re = (icfg[0]=='<' ? itmp+"\n" : '') + re + (icfg[0]=='>' ? itmp+"\n" : ''); 
    }
    return re;    
}
// 得到类别数组
function sordb_data(keyd){
    var a, key, data, pid, itm, i;
    a = keyd.split('-'); key = a[0]; 
    if(a.length==1){ // selec(字段)
        try{ 
            itm = 'data = _'+sordb_cfgs.mod+'_fields.';
            itm += (keyd.substr(0,4)=='exp_') ? sordb_cfgs.stype+'.'+key+'.cfgs;' : 'f.'+key+';';
            eval(itm); 
        }catch(ex){ data = ''; }
    }else{ // key-pid(类别-pid)
        pid = a[1]; data = ""; 
        eval("var _data = _"+key+"_data;");
        for(i=0;i<_data.length;i++){
            itm = _data[i]; 
            if(pid==itm[1]){
                data += (data.length>0 ? ';' : '')+itm[0]+"="+itm[2];
            }
        }  
    }
    return data.split(';');
}
// 得到关联数组
function sordb_drel(key,rel,pid){
    var dorg, drel, dpid, data='';
    if(pid=='(null)') return new Array('');
    eval("dorg = _"+key+"_data;");
    eval("drel = _"+rel+"_data;"); //p2012
    pdip = pid ? eval("dpid = _"+rel+"_data."+pid+";") : ','; 
    dpid = dpid ? dpid : ','; 
    for(i=0;i<dorg.length;i++){
        itm = dorg[i]; 
        if(dpid.indexOf(','+itm[0]+',')>=0){
            data += (data.length>0 ? ';' : '')+itm[0]+"="+itm[2];
        }
    } 
    return data.split(';');
}
// 得到区间数组
function sordb_darea(cfgs,unt){
    var arr, i, data, itm, old;
    arr = cfgs.split(','); //10,100,200,300,500,800,1000
    data = '~'+arr[0]+'=<'+arr[0]+unt; 
    old = arr[0];
    for(i=1;i<arr.length;i++){
        itm = old+"~"+arr[i];
        data += (data.length>0 ? ';' : '')+itm+"="+itm+unt;
        old = arr[i];
    }
    data += ';'+old+'~=>'+old+unt; 
    return data.split(';');
}
// 排序链接
// sordb_ordby("<a href='(url)' class='(act)'>点击</a>",'clicks','oasc,odesc,ondesc,1');
// sordb_ordby("<a href='(url)' class='(act)'>默认</a>",'salse','def-act,def,(def)');
function sordb_ordby(tpl,title,by,opts){
    var burl, vby, vdesc, opts, sdesc, sact, url;
    if(!tpl) tpl = sordb_cfgs.tpl;
    burl = sordb_mftUrl(sordb_cfgs.so,by);
    vby = urlPara('order','',sordb_cfgs.now);
    vdesc = urlPara('odesc','',sordb_cfgs.now);
    opts = opts.split(','); 
    if(opts[2]=='(def)'){
        sact = vby ? opts[1] : opts[0];
    }else{
        sact = opts[2];
        sdesc = opts[3].length>0 ? '&odesc=0' : '';
        if(vby==by){
            sdesc = vdesc.length>0==0 ? '&odesc=0' : '';
            sact = vdesc.length>0==0 ? opts[1] : opts[0];
        }
        burl += '&order=' + by + sdesc;
    }
    burl = tpl.replace('(url)',burl).replace('(title)',title).replace('(act)',sact);
    return sordb_mftUrl(burl); 
}
// 类别的pid-lay关系:p1012,p2012
// btype = sordb_typeLay('china', urlPara('china','')).split(',')
function sordb_typeLay(key,val){
    var re = val ? dataLays(key,val) : '';
    return re ? re.substr(0,re.length-1).replace(/;/g,',') : '(null)';
}
// 得到所有可用扩展字段
function sordb_exFields(){
    var aprops,k2,s='',a=new Array();
    if(sordb_cfgs.stype){
        try{ eval('aprops = _'+sordb_cfgs.mod+'_fields.'+sordb_cfgs.stype); }
        catch(ex){ aprops = null; }
        if(aprops){
            for(k2 in aprops){ 
                a.push(k2);
        }   }
    }
    return a;
}
// 格式化url
function sordb_mftUrl(url,key,val,cb){
    var a,b,c,i,j,keys,k;
    url = urlRep(url,key,val);
    // mkv,stype
    // 处理关联字段:stype:brand,price;type2:fff1,fff2
    if(sordb_cfgs.rel){
        a = sordb_cfgs.rel.split(';');
        for(i=0;i<a.length;i++){
            b = a[i].split(':');
            if(b[0]==key){
                c = b[1].split(',');
                for(j=0;j<c.length;j++){
                    if(c[j]=='exp_*' && sordb_cfgs.stype){
                        keys = sordb_exFields();
                        for(k=0;k<keys.length;k++){
                            url = urlRep(url,keys[k]);
                        }
                    }else{
                        url = urlRep(url,c[j]);
                    }
            }   }
    }   }
    return cb ? eval(""+cb+"('"+url+"');") : url; 
}
// 设置链接,如果为空则隐藏相关父标签
function sordb_setLinks(key,val,tag){
    if(!tag) tag = 'p';
    if(val){
        $('#'+key).html(val); 
    }else{
        //$('#'+key).parent().hide();
        $('#'+key).closest(tag).hide();
    } 
}
function sordb_hideClear(cid,css){
    var i, re, n=0, ub=',mkv,stype,page,', flag='';
    re = sordb_cfgs.so.match(new RegExp("[\?\&][^\?\&]+=[^\?\&]+","g")); 
    if(re){
        for(i = 0; i < re.length; i++){
            t = re[i].substring(1).split('='); 
            if(ub.indexOf(t[0])<=0){
                n++; break;
            }
        }         
    }
    if(!n && cid) $((css ? '.' : '#')+cid).hide();
    return n;
}

//var tpl = "<p class='tree_(lever) (key)'>(lay)<a href='(key)'>(letter)(title)</a></p>";
//tpl,key,letter,sordb_sotype(tpl,pid,act,clear) /*[　][＋][－][｜][├][└]  */
function sotree_init(tpl,key,letter,cid){ 
    var data,s='',s0,i,itm,n,b,lay,j,pid='0';
    eval("data = _"+key+"_data;");
    this.b = new Array(0,0,0,0,0,0,0,0);
    for(i=0;i<data.length;i++){
        itm = data[i]; lay = '';
        n = dataNext(data,i);
        b = this.b[itm[3]] = dataBrother(data,i);
        if(itm[3]>1){
            for(j=1;j<itm[3];j++){
                lay += this.b[j] ? '<i class="line">｜</i>' : '<i class="blank">　</i>';
            }
        }
        if(n){
            lay += '<i class="add" onclick=\"sotree_fold(this,'+itm[3]+')\">＋</i>';
        }else{
            lay += b ? '<i class="item">├</i>' : '<i class="end">└</i>';    
        }
        css = itm[3]==1 ? '' : 'none';
        s0 = tpl.replace(/\(key\)/g,itm[0]).replace('(title)',itm[2]).replace('(lay)',lay);
        s0 = s0.replace('(pid)',itm[1]).replace('(level)',itm[3]).replace('(css)',css);
        if(itm[3]==1){
            s0 = s0.replace('(letter)',itm[5]);
        }else{
            s0 = s0.replace('[(letter)]','').replace('((letter))','').replace('(letter)','');
        }
        s += s0;
    }
    if(cid) $('#'+cid).html(s);
    else return s;
}
function sotree_fold(em,d){ 
    var e,t,ishide,n,char=''; 
    e = $(em).parent();
    t = $(e).prop("tagName");
    ishide = $(em).hasClass("add");
    if($(em).html()=='＋') char = '－';
    if($(em).html()=='－') char = '＋';
    $(em).html(char);
    $(em).toggleClass('add');
    $(em).toggleClass('sub');    
    while(n=$(e).next()){
        if($(n).hasClass("tree_"+d)) break; //同级别 
        if(!(t==$(n).prop("tagName"))) break; //非tree元素（否则最后一个死循环）
        if(ishide){ //只展开一级
             if($(n).toggleClass('tree_'+(d+1))) $(n).toggleClass('none'); 
        }else{
            $(n).toggleClass('none');    
        }
        e = n;
    }
}
function sotree_act(cid,dkey,val,act){
    var lay,i,p1,a1;
    if(!act) act = 'act';
    lay = dataLays(dkey,val).split(';'); 
    if(lay.length>0){
        for(i=0;i<lay.length;i++){
            p1 = $('#'+cid).find('p.k_'+lay[i])[0];
            i1 = $(p1).find('i:first'); 
            $(i1).click(); //trigger("click");                
            if(lay[i]==val){
                $(p1).find('a:first').toggleClass(act);
                break;
            }
        }
    }
}
// 显示所有
function sotree_open(cid,val,act){
    if(!act) act = 'act';
    $('#'+cid+' p.none').toggleClass('none'); //展开所有
    if(val) $('#'+cid+' p.k_'+val).find('a').toggleClass(act); //当前选种项
    $('#'+cid+' i.add').html('－'); 
    $('#'+cid+' i.add').toggleClass('sub');
    $('#'+cid+' i.add').toggleClass('add');    
}
// 
/*

*/
