
var _cnt=0;

function chkRein(fmid, exp){
    if(!exp){
        let uname = $('#uname').val();
        if(uname.length<2){ alert('模拟账号:至少填两个字符!'); return false; }  
    }
    return saveForm(fmid, exp);
}

function saveForm(fmid, exp){
    $('#dtip1').find('.error').hide();
    $('#dtip1').find('.okey').hide();
    $('#row_load').show();
    $.ajax({ 
        url: saveUrl+''+_cnt+(exp?'&'+exp:''), 
        cache: false,
        data: $('#'+fmid).serialize(),
        dataType: 'json',
        success:function(data){
            $('#row_load').hide();
            $('#dtip1').find('.text').html(data.errmsg);
            if(data.errno){
                $('#dtip1').find('.error').show();
            }else{
                $('#dtip1').find('.okey').show();
            }
            opToast('dtip1', function(){
                //if(!data.errno){ location.reload(); }
            }, 2300);
        },
        error:function(){
            $('#row_load').hide();
            alert('Server Error!');
        }
    })
    _cnt++;
    return false;
}

//*/
