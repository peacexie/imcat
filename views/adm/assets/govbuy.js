
//var deelKeys = ',';

function setCheck(e){
    setTimeout(function(){ 
        var text = $(e).html();
        $(e).before(text).remove(); 
    }, 500);
}

function setModel(key){
    let ichk = $('#chk_'+key),  iimp = $('#imp_'+key), 
        curl = $(ichk).attr('url'), purl = $(iimp).attr('url'), 
        urp = $('#imp_link').attr('urp'),
        modid = $('#fm_'+key).val();
    $(ichk).prop('href',curl+'&'+urp+'&key='+key+'&modid='+modid);
    $(iimp).prop('href',purl+'&'+urp+'&key='+key+'&modid='+modid);
}


