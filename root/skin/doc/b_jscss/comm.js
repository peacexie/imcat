

function jsactMenu(menuid){
	if(_cbase.jsrun.menuid){
		menuid = _cbase.jsrun.menuid;
	}else if(!menuid){
		var a = _cbase.run.mkv.replace('.','-').split('-');
		menuid = a[0];
	} 
	//if(',docs: demo,news,topic,keres,cargo,'.indexOf(menuid)>0) menuid = 'docs';
    //if(',uplog: uplog,'.indexOf(menuid)>0) menuid = 'start';
	//if(',tools: tools,'.indexOf(menuid)>0) menuid = 'tester';
	var e = jsElm.jeID('idf_'+menuid); 
	if(e) e.className = 'act';
}

function js_aheight(){ 
	if($('.pgf_side2').html()){
		if($('.pgf_mcon2').height()+20>$('.pgf_mid2').height()){
			$('.pgf_mid2').height($('.pgf_mcon2').height()+20); 
		}		
	}else{
		if($('.pgf_side').height()>$('.pgf_mid').height()){
			$('.pgf_mid').height($('.pgf_side').height()); 
		}
	}
}
