
// flow/admin/files.php 翻译相关

function resetNav(part){
    $('.tbbar1 input').on('change',function(elm){
        resetList(part, this); 
    })
}

function resetList(part, elm){
    var ev = $(elm).val(); // fr,ru
    $('.tblist tr').each(function(no, tr){
        if(no==0) return true;
        if(part=='imcat') $(tr).hide();
        var fp = $(tr).find('a').eq(0).text(),
            url = $(tr).find('a').eq(1).prop('href');
        if(fp.indexOf('.maobak')>0) return true;
        if(part=='imcat' && fp.indexOf('-'+ev)>0){
            $(tr).show();
        } // 
        var i1 = $(tr).find('td').eq(4).find('i').eq(0),
            i2 = $(tr).find('td').eq(4).find('i').eq(1),
            iurl = url.replace('=down','=trans&to='+ev),
            a1 = "<b class='hand c369' uaj='"+iurl+"'>Trans.</b>";
        $(i2).html(a1);  //134D9D
    });
    $('.tblist tr').each(function(no, tr){
        var a3 = $(tr).find('b').eq(0);
        $(a3).click(function(){
            doTrans(a3);
        });
    });
}
function doTrans(a3){
    var url = $(a3).attr('uaj');
    $.ajax({
        url:url,
        success:function(res){
            $(a3).parent().addClass('c693').html(res);
        }
    }); 
}
