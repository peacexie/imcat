

function jsactMenu(menuid){
    var e = jsElm.jeID('idf_'+cm); 
    if(e) e.className = 'act';
    var sid = ck ? ck : cm[0]+'home'; 
    var s = jsElm.jeID('ids_'+sid);
    if(s) s.className = 'act';
}

function jsactInread(pid){
    jsImp('/plus/coms/inread.php?pid='+pid+'&'+_cbase.safil.url);
}

function jsqaSearch(e){
    var key = $(e).val(); 
    $('#keywd').prop('name',key);
}

