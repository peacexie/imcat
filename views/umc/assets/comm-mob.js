
function jsactMenu(menuid){
    if(!menuid){
        var a = _cbase.run.mkv.replace('.','-').split('-');
        menuid = a[0];
    }
    var e = jsElm.jeID('idf_'+menuid); 
    if(e) e.className = 'act';
}

//wxscb_screen.addEventListener('touchstart',closeTop2Pops, false); 
