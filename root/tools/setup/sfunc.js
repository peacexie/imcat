

function upInit(){
	$('#alltips').hide();
	var n = 0;
	$("[type='button']").each(function(index, element) {
		var id = $(this).attr("id"); //jsLog(id);
		if(steps.indexOf(id)>0){
			$(this).prop('disabled',true);
			$(this).removeClass('btn');
			$(this).addClass('dis');
		}else if(n==0 && steps.indexOf(id)<=0){
			n++;
		}else{
			$(this).prop('disabled',true);
			$(this).removeClass('btn');
			$(this).addClass('dis');
		}
	});
}

function upDir(e){
	var id = $(e).attr("id");
	location.href = '?step='+id;
}
function upTip(e){
	var id = $(e).attr("id");
	var msg = $('#tips_'+id).html(); 
	$('#reinfo').html(msg);
}

/*
function clear1(){
	if(confirm("确定要清空数据吗？")){
		document.main.text1.value="";
	}
}
*/

function chkIdpass(e,no,len){
	var tmp = $(e).val().replace(/\W/g, ""); //jsLog(tmp);
	if(simpass.indexOf(tmp)>0 || tmp.length<len){
		tmp = orgcfgs[no];
		alert('帐号密码不规范：\n字母/数字/下划线组成, 且字母开头\n帐号:3~15个字符, 密码:6~24个字符\n且不能是如下简单字串:\n'+simpass);
	}
	$(e).val(tmp);
}
function chkDbname(){
	var msg = $('#dbname').val()==$('#dbnold').val() ? '创建' : '修改';
	$('#bdbedit').val(msg);
}

function setStp12(step){
	var url = '?step='+step+'&act='+steps[step]+'&'+jsRnd();
	$.get(url, function(re){
		re = setJSON(step,re);
		if(re.res=='OK'){
			setState('step'+step,2,step); 
		}else{ 
			setState(step); 
		}
	});
}

function setStp34(step,id){
	if(step){
		var tabs = step==3 ? base_tabs : demo_tabs;
		var arr = tabs.split(',');
		var id = id ? id : 0; var tab = arr[id]; 
		var url = '?step='+step+'&act=Imps&tab='+tab+'&'+jsRnd();
		$.get(url, function(re){ 
			re = setJSON(step,re,tab);
			if(re.res=='OK'){
				id++; 
				if(id<arr.length){
					setTimeout("setStp34("+step+","+id+")",10);	
				}else{
					setTimeout("setStp34(0,"+step+")",10);
				}
			}else{ setState(step); }
		});
	}else{ //Finish //console.log(re);
		setJSON(id,"{'res':'OK','msg':''}");
		setState('step'+id,2,id); //step = id;
	}
}

function setStp5s(step,send){
	$('#xform').toggle();
	$('#xinfo').toggle();	
	if(!send) return;
	var name = $('#name').val(), uid = $('#uid').val(), upw = $('#upw').val();
	var url = '?step='+step+'&act=Idpw&uid='+uid+'&upw='+upw+'&name='+encodeURIComponent(name)+'&'+jsRnd();
	$.get(url, function(re){ 
		re = setJSON(step,re);
		if(re.res=='OK'){
			setState('step'+step,2,step); 
			var urla = ''+_cbase.run.roots+'/run/adm.php';
			urla = '<a href="'+urla+'" target="_blank" class="c30F">'+urla+'</a>';
			var urlh = ''+_cbase.run.rmain+'';
			urlh = '<a href="'+urlh+'" target="_blank" class="c30F">'+urlh+'</a>';
			var msg = '<a class="f14 fB">['+name+']</a> 安装结束,请不要刷新!';
			msg += '<br>帐号:[<span class="cF03 f16">'+uid+'</span>] 密码:[<span class="cF03 f16">'+upw+'</span>] ';
			msg += '<br>前台地址: '+urlh+'';
			msg += '<br>后台登录地址: '+urla+'<hr>';
			$('#reinfo').html(msg+$('#reinfo').html());
		}else{ setState(step); }
	});
}

function setMain(step){
	if(!nowStep){ $('#reinfo').html(''); } nowStep = step;
	$('#resone td').eq(0).html('正在处理:[第'+step+'步]');	
	if(step>1){
		var html = $('#step'+(step-1)+' td').eq(2).html();
		if(html.indexOf('isYes')<0) { alert('请按先后顺序执行！'); return; }
	}
	if(step<5) loadShow(); // tip : 正在处理，请稍候…… 
	if(step==1||step==2){
		setStp12(step);
	}else if(step==3||step==4){
		setStp34(step);
	}else if(step==5){
		setStp5s(step);
	}
}

function setState(trPart,tdNo,step){ 
	if(!trPart){ //初始化
		for(var i=1;i<=5;i++){
			eval('var tmp=step'+i+';');
			if(tmp=='OK'){ setState('step'+i,2,i); }	
			else{ break; }
		}
		$('#resone td').eq(0).html(fRes);		
	}else if(isObj(trPart,'n')){ //setp:出错
		$('#resone td').eq(0).html('[第'+trPart+'步]:处理失败');	
		$('#step'+' td').eq(2).html(fNO.replace('No','错误'));	
	}else{ //成功 if(trPart)
		var url = '?step='+step+'&act=Mark&'+jsRnd();
		$.get(url, function(re){ 
			$('#'+trPart+' td').eq(tdNo).html(fYES);
			$('#resone td').eq(0).html('已处理:[第'+step+'步]');
			if(step<5){
				$('#step'+(step+1)+' input').eq(0).val('-开始-');
			}
		});
	}
	loadStop();
}

function setJSON(step,data,tab){ 
	try{ eval('var re = '+data+''); } 
	catch(e){ 
		var re = {};
		re.res = e.name;
		re.msg = e.name+' @ '+e.message+' ::: '+data; 
	} //jsLog(re);
	msg = (tab ? '[表'+tab+']导入结果: ' : '[第'+step+'步]处理结果: ')+re.res+re.msg+'<br>';
	$('#reinfo').html(msg+$('#reinfo').html());
	return re;
}

// 定时器-显示
function loadShow(){
	$('#idLoad').show(); //jsLog('a, ');
	$('#idLoad img').toggle();
	otimer = setTimeout("loadShow()",1000); 
}
// 定时器-清除
function loadStop(){
	$('#idLoad').hide();
	clearTimeout(otimer);
}
//otimer = setLoading("loadShow()",1000);
//clearTimeout(otimer);
