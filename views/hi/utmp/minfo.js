
var _cnt=0;

function chkBase(fmid){
    let mname = $('#mname').val();
    if(mname.length<2){ alert('姓名:至少填两个字!'); return false; }
    return saveForm(fmid);
}

function saveForm(fmid, $exp){
    $('#dtip1').find('.error').hide();
    $('#dtip1').find('.okey').hide();
    $('#row_load').show();
    $.ajax({ 
        url: saveUrl+''+_cnt+($exp?'&'+$exp:''), 
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
                if(!data.errno){ location.reload(); }
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

//*
function sndfMobvc(act){
    let vcode=$('#vcode2').val(), mcode = $('#mcode').val(), mtel = $('#mtel').val();
    if(act=='smsg' && $('#btnSms').hasClass('btn-gray')){
        if(!vcode || !mtel){ alert('请填写图片码和手机'); }
        return false;
    }
    $('#dtip1').find('.error').hide();
    $('#dtip1').find('.okey').hide();
    $('#row_load').show();
    $.ajax({ 
        url: mobsmapi+''+_cnt, //url: (act=='smsg'?mobsmapi:mobvcapi) + '-'+_cnt, 
        cache: false,
        data: $('#fmmobvc').serialize(),
        dataType: 'json',
        success:function(data){
            $('#row_load').hide();
            $('#dtip1').find('.text').html(data.errmsg);
            if(data.errno){
                $('#dtip1').find('.error').show();
            }else{
                $('#dtip1').find('.okey').show();
                if(act=='smsg'){ 
                    $('#btnSms').removeClass('btn-act').addClass('btn-gray').addClass('btn-checked');
                }
            }
            opToast('dtip1', function(){}, 2300);
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
