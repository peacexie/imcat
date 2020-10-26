
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
function opToast(id){ // tipd1, element
    if(typeof(id)=='string'){
        var pe = $('#'+id);
        $(pe).show(200);
        setTimeout(function(){opToast(pe)},1500);
    }else{
        $(id).hide(200);
    }
}

