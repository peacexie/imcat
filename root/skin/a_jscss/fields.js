
function gf_setvMust(e){
	var fmreg = jsElm.jeID('fm[vreg]').value; 
	jsElm.jeID('fm[vreg]').value = (e.value=='nul' ? 'nul:' : '')+fmreg.replace('nul:','');
}

// 变更[字段类型]后设置[字段控件]
function gf_setfmType(e){
	var val = e.value;
	var cfgs = {};
	cfgs.input = ",winpop,datetm,map"; //color,
	cfgs.text = ",editor,pics,pick";
	cfgs.hidden = ",color";
	if(val=='input' || val=='text' || val=='hidden'){
		eval('var estr = cfgs.'+val+';');
	}else{
		estr = '';
	}
	var efm = jsElm.jeID('fm[fmextra]'); 
	efm.options[0].selected = true;
	efm.options.length = 1;
	ebak = jsElm.jeID('fmextra_bak'); 
	var ops = ebak.getElementsByTagName('option');
	for(var i=1;i<ops.length;i++){
		if(estr.indexOf(ops[i].value)>0)
		efm.options.add(new Option(ebak[i].text,ebak[i].value)); 
	}
}

// 选择:参考字段
// gf_setType('world|input|winpop')
function gf_setDemoField(e){
	var a = e.split('|');
	jsElm.jeID('fm[kid]').value = a[0];
	jsElm.jeID('fm[type]').value = a[1];
	jsElm.jeID('fm[from]').value = a[0];
	gf_setfmType(jsElm.jeID('fm[type]'));
	jsElm.jeID('fm[fmextra]').value = a[2];
	jsElm.jeID('fm[etab]').value = a[3];
}

function gf_setvType(){
	var v = jsElm.jeID('fm_vtype').value;
	var v1 = jsElm.jeID('fm_vlen').value;
	var v2 = jsElm.jeID('fm[vmax]').value;
	jsElm.jeID('fm[vreg]').value = v;
	if('(str:tit:key:)'.indexOf(v)>0){ 
		jsElm.jeID('fm[vreg]').value += v1+'-'+v2;
	}else{
	  var tab = {
		tel   : '7-24',
		email : '6-255', // x@g.cn 
		uri   : '8-255', // ftp:g.cn
		file  : '7-120', // C:\f.as
		image : '8-120', // C:\f.as
	  };
	  if(v.length==0){
		jsElm.jeID('fm_vlen').value = 1;
		jsElm.jeID('fm[vmax]').value = 255;
	  }else if(v.indexOf('fix:')==0){ 
		t = v.substring(4);
		eval('str = tab.'+t+';');
		var arr = str.split('-');
		jsElm.jeID('fm_vlen').value = arr[0];
		jsElm.jeID('fm[vmax]').value = arr[1];
	  }else if('vimg:'.indexOf(v)==0){
		jsElm.jeID('fm_vlen').value = 3;
		jsElm.jeID('fm[vmax]').value = 6;
	  }else{
		jsElm.jeID('fm_vlen').value = 1;
		jsElm.jeID('fm[vmax]').value = 12;
	  }

	}
}