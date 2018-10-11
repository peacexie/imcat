
// onChange=\"mitmDone(this)\" 

function mitmCheck(val,alert=0){
    val = val.replace(/(^\s*)|(\s*$)/g, "");
    if(val.length<1){
        if(alert) alert('Error!');
        return false;
    }else{
        return true;
    } // val.match(/\S{2,255}/)
}
function mitmInit(key){
    var end = $('#'+key+'Box div:last');
    if(end.length==0){
        mitmInsert(key);
    }else{
        var val = $(end).find('input:first').val();
        if(mitmCheck(val)){
            mitmInsert(key);
        }
    }
    setTimeout("mitmInit('"+key+"')",1023);
}
function mitmInsert(key){
    var tpl = $('#'+key+'Tpl').html();
    var no = Date.parse(new Date());
    var html = tpl.replace(/no_1/g,no);
    $('#'+key+'Box').append(html); 
}
