
function jsactMenu(menuid){
	if(_cbase.jsrun.menuid){
		menuid = _cbase.jsrun.menuid;
	}else if(!menuid){
		var a = _cbase.run.mkv.replace('.','-').split('-');
		menuid = a[0];
	}
	var e = jsElm.jeID('idf_'+menuid); 
	if(e) e.className = 'act';
}
