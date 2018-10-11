

function jsActNav(clsid){
    $('nav ul.'+clsid).toggle(300);
    $('nav i.'+clsid).toggleClass('fa-expand');
    $('nav i.'+clsid).toggleClass('fa-compress');    
}

function jsactMenu(menuid){
    if(!menuid){
        var a = _cbase.run.mkv.replace('.','-').split('-');
        menuid = a[0];
    }
    var e = jsElm.jeID('idf_'+menuid); 
    if(e) e.className = 'act';
}