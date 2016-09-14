
function relStore(fm2){
	var sid = 'sid__'+fm2.replace('[','_').replace(']','_');
	if(!jsElm.jeID(sid)){ 
		var html = $(jsElm.jeID(fm2)).html();
		$("body").append("<select id='"+sid+"' style='display:none;'>"+html+"</select>");	
	}
	return $('#'+sid);
}

function relInit(relid,fm1,fm2){
	var rcfg, o1 = jsElm.jeID(fm1), o2 = jsElm.jeID(fm2); 
	eval('rcfg = _'+relid+'_data;'); 
	var key='', val='', html='', _bak = relStore(fm2);
	for(var k in rcfg){ 
		if($(o1).val()==k){
			key = k;
			val = rcfg[k];
			break;
		}
	} //jsLog(key);
	if(val.length>1){
		$(_bak).find('option').each(function(index, element) { //jsLog($(this).html());
			var v2 = $(this).val(), text = $(this).html(), sel = v2==$(o2).val() ? 'selected' : ''; 
			if(v2.length<=1 || val.indexOf(v2)>=0) html += "<option value='"+v2+"' "+sel+">"+text+"</option>"; 
		});
	}else{
		html = $(_bak).html();	
	}
	$(jsElm.jeID(fm2)).html(html);	
	//jsLog(key+':'+val);
}

function relCatid(pid,mod,kid,catid){
	$(".ins_catid_rows").remove(); //移除添加的项目
	catid = catid ? catid : 'fm[catid]'; 
	var val = $(jsElm.jeID(catid)).val();
	if(val.length==0) return; //
	var url = _cbase.run.roots+'/plus/ajax/cajax.php?act=cfield&mod='+mod+'&catid='+val+'&fm[kid]='+kid;
	$.get(url, function (res) { 
		var eobj = $(jsElm.jeID(pid)).parent().parent(); 
		if(res.length>12){  
			var reg = new RegExp('<tr>', "gi"); //添加标记,便于控制
			res = res.replace(reg,'<tr class="ins_catid_rows">');
			$(eobj).after(res); //before
		}
	}); 
}

// wpop_pick.js
// wpop_data.js

// *** wpop_pick.js: color颜色,map地图,popDiv =============================================================
// color颜色
function colorPick(color,title){
	var popid = color+'_pop'; 
	jeShow(popid);
	if(jsElm.jeID(popid).innerHTML.length<24){
		var fpara = '?color='+color+'&title='+title+'&'+jsRnd();
		var fname = ' id="'+jsKey(color)+'_win" name="'+jsKey(color)+'_win" width="100%" height="100%" ';
		var frame = '<iframe '+fname+' src="'+_cbase.run.roots+'/plus/api/color.php'+fpara+'" frameborder="0" scrolling="no"></iframe>'
		jsElm.jeID(popid).innerHTML ='<span class="color_in">'+frame+'</span>';
	}else{
		window.frames[jsKey(color)+'_win'].resOrg.style.color = '#'+jsElm.jeID(color).value;
	}
}
function colorSet(color,title){
	jsElm.jeID('fm['+title+']').style.color = '#'+color;
}
// map地图
function mapPick(type,fid,w,h){
	var point = jsElm.jeID(fid).value; 
	url = _cbase.run.roots+'/plus/map/index.php?type='+type+'&act=pick&point='+point+'&title='+fid+'';
	if(!w) w = 720; if(!h) h = 480;
	popOpen(lang('jcore.pop_pickmap'),url,w,h);
}
// 检查重复 ：id='fm_repeat_' onclick="repCheck('news','title','fid');"
function repeatCheck(mod,fid,kid){
	var para = 'act=infoRepeat&mod='+mod+'&fid='+fid+'&kwd='+jsElm.jeID('fm['+fid+']').value+'';
	jQuery.getScript(_cbase.run.roots+'/plus/ajax/cajax.php?'+para,function(){ 
		var re = _repeat_res=='success' ? lang('jcore.pop_repeat') : _repeat_res;
		layer.tips(re, '#fm_repeat_'+kid, {tips:3});
	});
}
// 资料选取Pick
function pickOpen(modid,retitle,refval,refname,cntre,exparas){
	var emod = jsElm.jeID(modid); 
	var mod = emod ? emod.value : modid;
	if(mod==''){ layer.tips(lang('jcore.pop_pickmod'),'#'+modid); return; }
	var url = _cbase.run.roots+'/plus/ajax/pick.php?';
	refval = encodeURIComponent(refval);
	refname = encodeURIComponent(refname);
	cntre = cntre ? cntre : 1;
	//exparas: fso,ford,fshow,cntre,cshow ; &vdef='+vdef+'
	url += 'mod='+mod+'&retitle='+retitle+'&refval='+refval+'&refname='+refname+'&cntre='+cntre+'';
	popOpen(lang('jcore.pop_infopick'),url,640,320);
}
function pickOne(e){
	var itm = pickRinfo(e); 
	parent.jsElm.jeID(pick_refval).value = itm[0];
	parent.jsElm.jeID(pick_refname).value = itm[1];
	popClose();
	return;
}
function pickMul(e,isdel){ 
	if(isdel){
		$(e).parent().remove();	
		return;
	}
	var itm = pickRinfo(e); //var chk = e.checked;
	pfield = pick_refval+'[]'; //pout = pick_refname;
	if(!e.checked){
		$("[name='"+pfield+"']",window.parent.document).each(function(i, ei) { 
			if($(ei).val()==itm[0]){ 
				var p = $(ei).parent().remove(); //pvals += $(e).val()+',';
				$('#sel_cnt').html(parseInt($('#sel_cnt').html())-1);
				return;
			}
		}); 
	}else{
		var has = $("[value='"+itm[0]+"']",window.parent.document); //jsLog(has);
		if(has && $(has[0]).prop('name')==pfield) return;
		if(e.checked && parseInt($('#sel_cnt').html())>=pick_max){ 
			alert(lang('jcore.pop_maxn',pick_max));
			return false; 
		}
		var html = "<span><input name='"+pfield+"' type='checkbox' class='rdcb' onClick='pickMul(this,1)' value='"+itm[0]+"' checked />"+itm[1]+"</span>";
		$("[id='"+pick_refname+"']",window.parent.document).append(html);
		$('#sel_cnt').html(parseInt($('#sel_cnt').html())+1);  
	}
	return;
}
function pickAll(e){
	var rows = $("#fmlist tr").length-1;
	var inow = parseInt($('#sel_cnt').html());
	if(inow+rows>pick_max){
		alert(lang('jcore.pop_maxm',pick_max));
		return false; 
	}else{ //jsLog(name); //
		fmSelAll(e);
		$('#fmlist tr input').each(function(i, ei) {
			var name = $(ei).prop('name').replace('fs[','').replace(']',''); if(!name || name=='fs_act') return true;
			pickMul(ei);
		});
	}
}
function pickInit(n){
	var cnt = 0, pvals = ',', pfield = n>1 ? pick_refval+'[]' : pick_refval;
	$("[name='"+pfield+"']",window.parent.document).each(function(i, e) { 
		pvals += $(e).val()+',';
		cnt++;
	}); //jsLog(pvals+':'+n);
	if(n) $('#sel_cnt').html(cnt); 
	$('#fmlist tr input').each(function(i, e) {
        var name = $(e).prop('name').replace('fs[','').replace(']',''); if(!name) return true;
		if(pvals.indexOf(name)>0) $(e).attr("checked", true);
    });
}
function pickRinfo(e){
	var key = $(e).prop('name').replace('fs[','').replace(']',''); 
	var title = jsElm.jeID(pick_retitle+'_'+key).innerText; //jsLog(key+'::'+title+':'+pick_retitle);
	return [key,title];
}

// Init 初始化
function popInit(fid,mod,w,n,def,cstr,cb){
	var fid2 = jsKey(fid);
	if(!cstr) cstr = '{}';
	eval("var mcfg=_"+mod+"_cfg;"); //"+fid2+"_obj=
	var cfg = dataCfgs('pop',cstr);
	var pid = cfg.pid == '' ? '0' : cfg.pid; cfg.pid = pid;
	var sobj, obj = {};  
	sobj = dataSuns(mod,pid);
	obj.i = sobj.i;
	obj.c = cfg; //obj.m = mcfg;
	obj.n = n; 
	obj.pid = pid;
	obj.mpage = 1200; //不同级别，一页最多显示个数:72,
	obj.mchar = 60; //同一级别，超过多少个按首字母显示:360
	obj.title = mcfg.title; 
	obj.dmin = sobj.dmin == 0 ? 1 : sobj.dmin;
	obj.dmax = sobj.dmax == 0 ? mcfg.deep : sobj.dmax; 
	if(pid!='0'){
		eval("var data = _"+mod+"_data;");
		obj.title = dataName(data,pid); 
	}
	eval(""+fid2+"_obj=obj;"); 
	var url = _cbase.run.roots+'/plus/api/types.php?fid='+fid+'&cb='+(cb?cb:'')+'';
	var act = " onClick=\"popOpen('"+obj.title+"','"+url+"')\"";
	var sty = 'class="wpop_icon txt" style="width:'+w+'px; background-position:'+(w-20)+'px -22px;"';
	var str = '<input id="'+fid+'" name="'+fid+'" type="hidden" value="'+def+'" />';
	var vstr = jsElm.jeID(fid2+'_pop').innerHTML; if(vstr.length>4) vstr = ' reg="'+vstr.replace('key:','str:')+'"';
	str += '<input id='+fid2+'_name name='+fid2+'_name type="text" value=""; '+sty+act+vstr+' />'; //reg="str:2-24" 
	jsElm.jeID(fid2+'_pop').innerHTML = str; 
	if(def.length>0){ //处理选中项目(名称)
		var str='', t='', g='', k=0; va=def.split(',');
		for(var i=0;i<va.length;i++){ 
			t = dataNams(obj.i,va[i]);
			if(t.length>0){
				str += g+dataNams(obj.i,va[i]);
				g = ', '; k++;
			}
		}
		if(n>1&&k>1){ str = str+lang('jcore.pop_all',k);}
		jsElm.jeID(fid2+'_name').value = str;
	}
}
function popOpen(title,url,w,h){
	var w = w ? w : 450;
	var h = h ? h : 360; 
	if(_cbase.sys_pop==4){ 
		var ops = {type:2, fix:false, maxmin:true, title:[title,true], area:[w+'px',h+'px'], content:[url], close:function(index){layer.close(index);}};
		layer.open(ops); // type:2, shade:[0], border:[5,0.7,'#0A246A',true], moveOut:true, offset:['50%','50%'],
	}else if(_cbase.sys_pop==3){ 
		tipsWindown(title,'iframe:'+url+'',w,h,'true','','true','content-boxCss');
	}else if(_cbase.sys_pop==2){ 
		$.webox({title:title, iframe:url, width:w, height:h, bgvisibel:false }); 
	}else{ //=3
		tipsWindown(title,'iframe:'+url+'',w,h,'true','','true','content-boxCss');
		//drag:      是否可以拖动(ture为是,false为否)
		//time:      自动关闭等待的时间，为空是则不自动关闭
		//showbg:      [可选参数]设置是否显示遮罩层(0为不显示,1为显示)
		//cssName:      [可选参数]附加class名称
	}
}
function popClose(){
	if(_cbase.sys_pop==4){ 
		var index = parent.layer.getFrameIndex(window.name);
		parent.layer.close(index);
	}else if(_cbase.sys_pop==3){ 
		$("#wtips-close", window.parent.document).trigger("click");
	}else if(_cbase.sys_pop==2){ 
		parent.$("#webox_close").trigger("click");
	}else{ //=3
		$("#wtips-close", window.parent.document).trigger("click");
		//$('#wtips-close',window.parent.parent.document).trigger("click");
		//window.parent.document.getElementById("#wtips-close")
		//parent.$("#wtips-close").trigger("click");
	}		
}

// *** wpop_data.js ======================================================================================
// 是否有子项目，data-item格式
// [key,pid,title,deep,frame,char]
// ['yxx7','c0735','永兴县',3,0,'Y']
function dataNext(data,k){
	var kitm,knxt;
	if(k==data.length-1) return false;
	kitm = data[k];
	knxt = data[k+1];
	return knxt[1]==kitm[0] ? true : false;
	/*
	var d = -1;
	for(var i=k;i<data.length;i++){
		var itm = data[i];
		if(i==k){
			d = itm[3]; 
			continue;
		}else{ // && lays.indexOf(itm[3])>=0
			if(itm[3]>d) return true;
			break;
		}
	}
	return false;*/
}
// 是否有兄弟项
function dataBrother(data,k){
	if(k==data.length-1) return false;
	kitm = data[k];
	for(var i=k+1;i<data.length;i++){
		var itm = data[i];
		if(itm[3]==kitm[3] && itm[1]==kitm[1]) return true; //级别和pid相同
		if(itm[3]<kitm[3]) return false; //比它级别小了肯定没有,退出提高效率！
		//if(itm[3]>kitm[3]) continue;
	}
	return false;
}
// 所有pid下的suns,返回array
function dataSuns(datu,pid,lays){
	if(isObj(datu,'s')) eval("var data = _"+datu+"_data;");
	else if(isObj(datu,'a')) var data = datu;
	else if(isObj(datu)){ return datu; }
	if(!pid) pid = '0'; if(!lays) lays = '123456789';
	if((pid=='0')&&(lays.length==9)){ 
		return {i:data,dmin:0,dmax:0}; //dmax:obj.dmax
	}
	var save = pid=='0' ? true : false;
	var arr = new Array(), deep = 0, dmin = 99, dmax = 0;
	for(var i=0;i<data.length;i++){
		var itm = data[i];
		if(pid.length!='0'&&pid==itm[0]){
			save = true;
			deep = itm[3]; 
			continue;
		}
		if(pid.length!='0'&&save&&(itm[3]<=deep)){ 
			break;
		}
		if(save&&lays.indexOf(itm[3])>=0){ 
			arr.push(itm);	
			dmin = Math.min(dmin,itm[3]);
			dmax = Math.max(dmax,itm[3]);
		}
	} 
	return {i:arr,dmin:dmin,dmax:dmax};
}
// Letter按首字母，分组
function dataLetter(data){
	var xn = 0, ch = '', a = new Array();
	for(var i=0;i<=26;i++){
		a[i] = new Array();
	}
	for(var i=0;i<data.length;i++){
		var itm = data[i];
		ch = data[i][5]; 
		if(ch=='') a[0].push(itm);
		else{ //string.fromCharCode(asc);
			xn = ch.charCodeAt()-64; 
			a[xn].push(itm);	
		}
	}
	return a;
}
// kid对应的lays
function dataLays(data,kid,cpid){
	if(isObj(data,'s')) eval("data = _"+data+"_data;");
	ida = kid.split(',');
	for(var i=0;i<ida.length;i++){
		if(ida.length>0) kid = ida[i];
	}
	var str = '', pid = kid, t = ''; //, flag = false
	for(var i=data.length-1;i>=0;i--){
		var itm = data[i];
		if(pid==itm[0]){
			pid = itm[1]; 
			str = itm[0]+';'+str;
			if(pid==cpid||pid=='0') break;
		}
	}
	return str;
}
// kid对应的Nams,湖南»郴州
function dataNams(data,kid){
	val = dataLays(data,kid);
	var str='', gap=''; 
	val = val.replace(new RegExp(';',"gm"),',');
	val = '(,'+val+','; // bj;bjxiaqu
	for(var i=0;i<data.length;i++){
		var itm = data[i]; 
		if(val.indexOf(','+itm[0]+',')>0){
			str += gap+itm[2];
			gap = '»'; // &laquo; &raquo; « »  
		}
	}
	return str;
}
// kid对应的Name
function dataName(data,kid){
	for(var i=0;i<data.length-1;i++){
		if(kid==data[i][0]){
			return data[i][2];
		}
	}
	return '';
}
// configs 配置
function dataCfgs(type,cfgu){
	if(isObj(cfgu)) var cfgr = cfgu;
	else eval("var cfgr = "+cfgu+";");
	if(type=='pop') var cfg = {pid:'',lays:'123456789',w:420,y:1};
	else if(type=='x') var cfg = {}; 
	else var cfg = {pid:'',lays:'123456789'}; 
	return jmJson(cfg,cfgr,1); 
}
// Test数组
function dataTest(data){
	var s = '';
	for(var i=0;i<data.length;i++){
		var itm = data[i]; 
		if(isObj(itm,'a')){
			s += '<br>';
			for(var j=0;j<itm.length;j++)
			s += ':'+itm[j];
		}else{
			s += ','+itm;	
		}
	}
	return s;
}


