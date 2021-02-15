
//bars/remove
function opSmenu(op, id){ // tipd1, element
    var pe = $('#'+id);
    if(op){
        $(pe).show(200);
    }else{
        $(pe).hide(200);
    }
}
function opTmenu(el){ // tipd1, element
    //
}
/*

*/

function opDialog(id){ // tipd1, element
    if(typeof(id)=='string'){
        $('#'+id).css({"display":''});
    }else{
        var pe = $(id).parent().parent();
        $(pe).css({"display":'none'});
    }
}
function opToast(id, cb, gap){ // tipd1, element
    if(!gap) gap = 1500;
    if(typeof(id)=='string'){
        var pe = $('#'+id);
        $(pe).show(200);
        setTimeout(function(){
            opToast(pe, cb, gap)
        }, gap);
    }else{
        $(id).hide(200);
        cb && cb();
    }
}

