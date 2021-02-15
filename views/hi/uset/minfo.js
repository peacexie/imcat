
var _cnt=0;

function chkWechat(fmid, exp){
    return saveForm(fmid, exp);
}
function chkBdmob(fmid, exp){
    if(!fmid && !exp){ alert('解绑无效!'); return false; }
    let mtel = $('#mtel').val();
    if(mtel.length==0){ alert('手机号:不能为空!'); return false; }
    let mcode = $('#mcode').val();
    if(mcode.length==0){ alert('短信码:不能为空!'); return false; }
    return saveForm(fmid, exp);
}
function chkExpw(fmid){
    let mname = $('#mname').val();
    if(mname.length==0){ alert('姓名:不能为空!'); return false; }
    //let opass = $('#opass').val();
    let npass = $('#npass').val();
    let ncheck = $('#ncheck').val();
    if(npass.length<6 || npass!=ncheck){ alert('密码:至少6个字符，且确认码与密码要一致!'); return false; }
    return saveForm(fmid);
}
function chkIdcard(fmid, exp){
    if(!fmid && !exp){ alert('解绑无效!'); return false; }
    if(fmid){
        let mname = $('#mname').val();
        if(mname.length==0){ alert('姓名:不能为空!'); return false; }
        let idcard = $('#idcard').val();
        if(idcard.length==0){ alert('身份证号:不能为空!'); return false; }        
    }
    return saveForm(fmid, exp);
}
function chkUdel(fmid){
    let mname = $('#mname').val();
    if(mname.length==0){ alert('姓名:不能为空!'); return false; }
    var rc = confirm("您将退出系统，是否确认?");
    if(rc){
        //
    }else{
        return false;
    }
    return saveForm(fmid);
}
function chkExmod(fmid){
    let tomod = $('#tomod').val();
    if(tomod.length==0){ alert('请选择:新模型!'); return false; }
    return saveForm(fmid);
}
function chkExid(fmid){
    let uname = $('#uname').val();
    if(uname.length<5){ alert('新账号:至少5个字符!'); return false; }
    return saveForm(fmid);
}
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
function setVcode(mod, click){
    var fimg = jsElm.jeID(mod+'_vimg'); 
    var para = "&mod="+mod+"&"+_cbase.safil.url;
    para += "&_r="+(new Date().getTime());
    fimg.setAttribute("src",_cbase.run.fbase+"?ajax-vimg"+para);
}
//*/
