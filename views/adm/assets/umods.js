
function laycb_umod_umod(mod, key, pid) {
    var url = _cbase.run.fbase + '?ajax-types&mod=uatt&pid='+pid+'';
    $('#now_attrs').html('');
    $.ajax({
        url: url,
        contentType: "application/json; charset=utf-8",
        success: function(data) {
            var arr = data; //jsLog(arr);
            if(arr.length){
                $('#now_attrs').append('<b>已有属性</b><br>');
                $.each(arr, function(i, row) {
                    $('#now_attrs').append(row.kid+':'+row.title+'<br>');
                });
            }else{
                //$(elm).hide();
            }
        },
        error: function (err) { 
            jsLog(err);
        }
    })    
}
