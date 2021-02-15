
var _timeFlag, _cnt=0;

function setPassinp(e){
    $(e).prop('type','password');
}

function chkApply(){
    let uname = $('#uname').val();
    if(uname.length<5){ alert('账号:至少5个字符!'); return false; }
    //let opass = $('#opass').val();
    let upass = $('#upass').val();
    let upchk = $('#upchk').val();
    if(upass.length<6 || upass!=upchk){ alert('密码:至少6个字符，且确认码与密码要一致!'); return false; }
    let umod = $('#umod').val();
    if(umod.length==0){ alert('请选择:类型!'); return false; }
    let mname = $('#mname').val();
    if(mname.length<2){ alert('姓名:至少填两个字!'); return false; }
    return saveForm();
}

function saveForm(){
    $('#dtip1').find('.error').hide();
    $('#dtip1').find('.okey').hide();
    $('#row_load').show();
    $.ajax({ 
        url: applyapi+'-'+_cnt, 
        cache: false,
        data: $('#fmapply').serialize(),
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
                if(!data.errno){ location.href = loginurl; }
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
function chkTel(isvc){
    let vcode=$('#vcode2').val(), mtel = $('#mtel').val();
    if(!vcode){
        //alert("请填写图片码");
        //$('#btnSms').removeClass('btn-act').addClass('btn-gray');
        //return false;
    }
    if(!(/^1\d{10}$/.test(mtel))){ 
        alert("手机号码有误，请重填");
        $('#btnSms').removeClass('btn-act').addClass('btn-gray');
    }else if(vcode){
        $('#btnSms').removeClass('btn-gray').addClass('btn-act');
    }
    return false; 
}
function sndfMobvc(act){
    let vcode=$('#vcode2').val(), mcode = $('#mcode').val(), mtel = $('#mtel').val();
    if(act=='smsg' && $('#btnSms').hasClass('btn-gray')){
        if(!vcode || !mtel){ alert('请填写图片码和手机'); }
        return false;
    }
    if(act=='login' && !$('#btnSms').hasClass('btn-checked')){
        alert('请填写图片码和手机，并发送手机短信');
        return false;
    }
    $('#dtip1').find('.error').hide();
    $('#dtip1').find('.okey').hide();
    $('#row_load').show();
    $.ajax({ 
        url: (act=='smsg'?mobsmapi:mobvcapi) + '-'+_cnt, 
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
            opToast('dtip1', function(){
                if(act!='smsg' && !data.errno){ location.reload(); }
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

function setVcode(mod, click){
    var fimg = jsElm.jeID(mod+'_vimg'); 
    var para = "&mod="+mod+"&"+_cbase.safil.url;
    para += "&_r="+(new Date().getTime());
    fimg.setAttribute("src",_cbase.run.fbase+"?ajax-vimg"+para);
}
