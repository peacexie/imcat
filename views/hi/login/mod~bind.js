
function bsndForm(act, params){
    $('#dtip1').find('.error').hide();
    $('#dtip1').find('.okey').hide();
    $('#row_load').show();
    $.ajax({ 
        url: bindapi+'?act='+act+params, 
        cache: false,
        data: $('#fmbind').serialize(),
        dataType: 'json',
        success:function(data){
            $('#row_load').hide();
            $('#dtip1').find('.text').html(data.errmsg);
            if(data.errno){
                $('#dtip1').find('.error').show();
            }else{
                $('#dtip1').find('.okey').show();
                if(act=='check'){
                    $('#corpName').val(data.cscorp.title);
                    $('#corp').val(data.cscorp.did);
                }
                //$('#row_name').show();
                //$('#row_tel').show();
            }
            opToast('dtip1', function(){
                if(act=='save'){ location.reload(); }
            }, 2300);
        },
        error:function(){
            $('#row_load').hide();
            alert('Server Error!');
        }
    })
}

function bchkForm(){
    let uname = $('#uname').val(), passwd = $('#passwd').val(),
        //mode = $('#mode').val(), umod = $('#umod').val(),
        mname = $('#mname').val(), mtel = $('#mtel').val();
    if(mname.length<2){
        alert('请填写姓名');
    }else if(uname.length<5){
        alert('请填写账号');
    }else if(passwd.length<6){
        alert('请填写密码');
    }else if(bdtag && mtel.length<7){
        alert('请填写电话');
    }else{
        bsndForm('save','');
    }
}
