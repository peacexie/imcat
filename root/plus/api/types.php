<?php
require(dirname(__FILE__).'/_config.php'); 

glbHtml::page('Types Pick',1);
glbHtml::page('imadm'); //adm
echo basJscss::imp('/skin/jslib/search.js');
//echo basJscss::imp('/skin/jslib/search.js');
?>
<style type="text/css">
.highlight { background: green; font-weight: bold; color: white; padding: 0px 8px; }
.table { }
</style>
<script>
// id(s)对应的项目名称(s),多选-已择项目
function popNames(){
	var val=jsElm.pdID(fid).value; 
	eval("var obj=_wp."+fid2+"_obj;"); 
	var str='', cbs='<span class="c999">'+lang('adm.types_nowsels')+'</span>', gap=''; cnt=0;
	val = val.replace(new RegExp(';',"gm"),',');
	val = '(,'+val+','; // bj;bjxiaqu
	for(var i=0;i<obj.i.length;i++){
		var itm = obj.i[i]; 
		if(val.indexOf(','+itm[0]+',')>0){
			var idnow = 'xid2_inow'+itm[0];
			var act = " onClick=\"popItmCheck('"+itm[0]+"',this)\" "; 
			cbs += '<label id="'+idnow+'" class="inblock ph2"><input type="checkbox" class="rdcb" '+act+' checked="checked">';
			cbs += '<span class="c00F">'+itm[2]+'</span></label>';
			str += gap+dataNams(obj.i,itm[0]);
			gap = ', '; // &laquo; &raquo; « » 
			cnt++; 
		}
	}
	if(obj.n>1&&cnt>1){ str = str+' ('+lang('adm.types_nowsels',cnt)+')';}
	jsElm.pdID(fid2+'_name').value = str;
	jsElm.jeID('xid2_now').innerHTML = cbs; 
	var cbnow = jsElm.jeID('xid2_now').getElementsByTagName('label'); 
	if(obj.n>1&&cbnow.length>0) jsElm.jeID('xid2_now').style.display = ''; 
	else jsElm.jeID('xid2_now').style.display = 'none'; 
}
// 级别梯 [«顶级«湖南«郴州«永兴] &laquo; &raquo; « » 
function popSetp(pid,type){ // type:init,add,del
	var val=jsElm.pdID(fid).value;
	var step = jsElm.jeID('xid2_step'), str = '';
	eval("var obj=_wp."+fid2+"_obj;"); 
	if(type=='del'){ 
		var labs = step.getElementsByTagName('label');
		for(var i=0;i<labs.length;i++){
			eid = labs[i].id.toString();
			if(eid==fid2+'_nstp_'+pid){
				for(var j=0;j<10;j++){
					var e = labs[i].nextSibling;
					try{
						if(e.tagName.toLowerCase()=='label') step.removeChild(e);
						else break;
					}catch(ex){break;}
				}break;
			}
		}
	}else if(type=='add'){ 
		var aopen = " onClick=\"popList('"+pid+"','del')\" "; 
		step.innerHTML += '<label id="'+fid2+'_nstp_'+pid+'" class="wpop_step" '+aopen+'>&laquo;'+dataName(obj.i,pid)+'</label>';
	}else{ //Init
		//if(val.length==0) return;
		val = dataLays(obj.i,val); 
		var a = val.split(';'), flag = false;
		var aopen = " onClick=\"popList('"+pid+"','del')\" "; 
		str = '<label id="'+fid2+'_nstp_'+pid+'" class="wpop_step" '+aopen+'>&laquo;'+lang('adm.types_top')+'</label>';	
		for(var i=0;i<a.length-2;i++){
			var kid = a[i];
			aopen = " onClick=\"popList('"+kid+"','del')\" "; 
			str += '<label id="'+fid2+'_nstp_'+kid+'" class="wpop_step" '+aopen+'>&laquo;'+dataName(obj.i,kid)+'</label>';	
			var suns = dataSuns(obj.i,kid); 
			if(suns.i.length<=obj.mpage){  
				popList(kid,suns); 
				flag = true; 
			}
		}
		step.innerHTML = str; 
		if(!flag) popList('0');
	}
	var labs = step.getElementsByTagName('label'); //重新计算一次//隐藏
	if(labs.length>1) step.style.display = ''; 
	else step.style.display = 'none'; 
}
// pop主列表
function popList(pid,sobj){	
	var val=jsElm.pdID(fid).value;
	eval("var obj=_wp."+fid2+"_obj;"); 
	if(!sobj||isObj(sobj,'s')) var suns = dataSuns(obj.i,pid); 
	else                       var suns = sobj; 
	var data = suns.i, chkend = false;; 
	var dmin = suns.dmin==0 ? obj.dmin : suns.dmin;
	var dmax = suns.dmax==0 ? obj.dmax : suns.dmax;
	var str='', gap='', deep = 0; 
	for(var i=0;i<data.length;i++){
		var itm = data[i];
		if(dmin==dmax){ //全部为同级，按字母头显示 &&data.length>obj.mchar
			var data2 = dataLetter(data);
			var _n = 0, ch = '';
			for(var k=0;k<=26;k++){
				if(data2[k].length>0){
					_n = k+64; //ch = _n==64 ? '(首选项)' : '首字母['+String.fromCharCode(_n)+']组';
					str += '<div class="wpop_chrdiv"><span class="wpop_chrspan">['+String.fromCharCode(_n)+']</span>';
					for(var j=0;j<data2[k].length;j++){
						str += popItem(data2[k][j],obj.n,'(def)');
					}
					str += '</div>';
				}
			}
			break;
		//}else if(dmin==dmax){ //全部为同级，全部显示
			//str += popItem(itm,obj.n,'(def)');
		}else if(data.length>obj.mpage){ //(不同级别)，>1页显示个数，只显示当前子类
			if(itm[3]==dmin){ 
				if(dataNext(data,i)) str += popItem(itm,obj.n,'step');
				else str += popItem(itm,obj.n,'(def)');
			}
		}else{ //(不同级别)，<1页显示个数 ...
			if(itm[3]<dmax){ //不是最小级别，显示树型，注意判断树型结束
				if(chkend){ //判断树型结束
					str += '</span>';
					chkend = false;
				}
				str += popItem(itm,obj.n,'tree',dmin);
				if(dmax-itm[3]==1){
					str += '<span class="tree_d'+(itm[3]-dmin+2)+'">';
					chkend = true;	//树型结束标记：需要判断结束-true
				}
				if(chkend&&i==data.length-1) str += '</span>'; //判断树型结束
			}else{ //为最小级别，挨个显示
				str += popItem(itm,obj.n,'(def)');	
			}
		}
	}
	jsElm.jeID('xid2_list').innerHTML = str;
	if(sobj=='add'||sobj=='del') popSetp(pid,sobj);
}
// pop一项html --- ['yxx7','c0735','永兴县',3,0,'Y']
function popItem(itm,n,flag,deep){ // flag:def,step,tree
	var val=jsElm.pdID(fid).value; 
	var idout = 'xid2_iout'+itm[0];
	var idin = 'xid2_iin'+itm[0];
	var idcb = 'xid2_icb'+itm[0];
	var str2 = str = act = css = chk = t = '';
	var vcmp = '(,'+val.replace(';',',')+',';
	if(vcmp.indexOf(','+itm[0]+',')>=0){ 
		css = 'c00F';
		chk = ' title="'+lang('adm.types_selected')+'"';
	} //flag='tree';
	if(itm[4]==1) css = 'c666'; // 结构分类灰色
	str = '<span id="'+idin+'" class="'+css+'">'+itm[2]+'</span>';//chk
	if(n>1){ // str2在不是step下使用
		chk = css=='c00F' ? ' checked="checked"' : ''; 
		if(itm[4]==0){ //非结构分类要这个
			act = " onClick=\"popItmCheck('"+itm[0]+"',this)\" ";
			str = '<input id="'+idcb+'" type="checkbox" class="rdcb" '+act+' '+chk+'>'+str;
		}
		str = '<label class="ph0">'+str+'</label>';
	}else{ //step下使用(单选)
		if(itm[4]==0){  //非结构分类要这个
			act = " onClick=\"popSetValue('set','"+itm[0]+"')\" ";
		}
		str = '<label class="ph0" '+act+'>'+str+'</label>';
	}
	if(flag=='step'){
		act = "<span class='tree_bg tree_dA' onClick=\"popList('"+itm[0]+"','add')\" > </span>";
		str = '<span id="'+idout+'" class="inblock ph5" >'+str+act+'</span>';	
	}else if(flag=='tree'){ //jsLog(flag);
		var dot = itm[3]-deep==0 ? 'A' : 'C'; // deep=dmin
		dot = '<span class="tree_bg tree_d'+dot+'"></span>';
		str = '<span id="'+idout+'" class="tree_d'+(itm[3]-deep+1)+'">'+dot+str+'</span>';	
	}else{ //tree
		str = '<span id="'+idout+'" class="inblock ph5">'+str+'</span>';	
	}
	return str;
}
function popItmCheck(kid,e){ 
	var val=jsElm.pdID(fid).value;
	var idnow = 'xid2_inow'+kid; 
	var idin = 'xid2_iin'+kid;
	var idcb = 'xid2_icb'+kid;
	var chked = e.checked; //alert(chked);
	eval("var obj=_wp."+fid2+"_obj;");  //n=obj.n, 
	if(chked){ // Add --- 从:未选-=>选 
		var cbnow = jsElm.jeID('xid2_now').getElementsByTagName('label'); 
		if(cbnow.length>obj.n-1){
			alert(lang('adm.types_tipmaxn',obj.n)); // '提示:最多只能选['+obj.n+']个'
			jsElm.jeID(idcb).checked = false;
			return;
		}
		var act = " onClick=\"popItmCheck('"+kid+"',this)\" "; 
		cbs = '<label id="'+idnow+'" class="inblock ph2"><input type="checkbox" class="rdcb" "'+act+'" checked="checked">';
		cbs += '<span class="c00F">'+dataName(obj.i,kid)+'</span></label>';
		jsElm.jeID('xid2_now').innerHTML += cbs; 
		jsElm.jeID(idin).className = 'c00F';
		popSetValue('add',kid);
	}else{ // Del --- 从:选-=>未选
		try{ jsElm.jeID(idnow).parentNode.removeChild(jsElm.jeID(idnow)); }
		catch(ex){}
		try{ jsElm.jeID(idin).className = ''; jsElm.jeID(idcb).checked = false; }
		catch(ex){}
		popSetValue('del',kid);
	}
}
function popSetValue(type,kval){ 
	var val=jsElm.pdID(fid).value;
	if(type=='clear'){
		jsElm.pdID(fid).value = '';
		jsElm.pdID(fid2+'_name').value = ''; //alert('xx');
		$('#xid2_close').trigger("click");
	}else if(type=='add'){
		if(val.length==0) val = kval;
		else val = val+','+kval; 
		val = val.replace(',,',',');
		if(val==',') val = '';
		jsElm.pdID(fid).value = val;
		popNames();
	}else if(type=='del'){
		val = val.replace(kval+',','').replace(','+kval,'');
		val = val.replace(',,',',');
		if(val==',' || val==kval) val = '';
		jsElm.pdID(fid).value = val; 
		popNames();
	}else{ //set单选 
		eval("var obj=_wp."+fid2+"_obj;");
		jsElm.pdID(fid).value = kval; 
		jsElm.pdID(fid2+'_name').value = dataNams(obj.i,kval);
		$('#xid2_close').trigger("click");
	}
	<?php $cb=basReq::val('cb'); echo $cb ? "window.parent.$cb(kval,jsElm.pdID(fid2+'_name').value);\n" : "\n"; ?>
}
</script>
<?php glbHtml::page('body',' style="padding:3px"'); ?>
<script>
var _wp = window.parent, _pd = parent.document;
var fid = urlPara('fid'), fid2 = jsKey(fid); 
str = '<table width="100%" border="0" cellpadding="3" cellspacing="3" class="table">';
str += '<tr><td><div class="w100 cF00 hand right tr" style="padding-top:3px;">'; 
str += '<span id="xid2_clear" class="ph2" onClick="popSetValue(\'clear\')" title="'+lang('adm.types_cltip')+'">'+lang('adm.types_clear')+'</span>';
str += '<span id="xid2_close" class="ph2" onClick="popClose()" title="'+lang('adm.types_cftip')+'">'+lang('adm.types_confirm')+'</span></div>';
str += '<span id="xid2_title" class="inblock"><input name="schVal" id="schVal" type="text"></span>';
str += '<input name="bsend" type="submit" class="btn" value="'+lang('adm.types_search')+'" onclick="schDone()" /></td></tr>';
str += '<tr><td id="xid2_now" class="h180"></td></tr>';
str += '<tr><td id="xid2_step" class="h180 tr"><span class="c999">'+lang('adm.types_new')+'</span></td></tr>';
str += '<tr><td id="xid2_list" class="h180" style="padding:5px 0 0 0;border-top:1px solid #CCC;">-list-</td></tr>';
str += '</table>';
document.write(str); // Table 初始化
popNames(); // 项目名称,多选-已择项目
popSetp('0'); // 级别梯,&List
</script>
</body>
</html>
