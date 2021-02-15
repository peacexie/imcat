
var _timeFlag, _cnt=0;

function sndfIdpwd(){
    $('#dtip1').find('.error').hide();
    $('#dtip1').find('.okey').hide();
    $('#row_load').show();
    $.ajax({ 
        url: idpwdapi+'-'+_cnt, 
        cache: false,
        data: $('#fmlogin').serialize(),
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

function checkScan(n){
    let gap = n<12 ? 2300 + n*300 : 6000;
    if(n>600){
        $('.qrpelm').html("<i class='fa fa-qrcode'></i>超时，刷新重新扫码")
        return;
    }
    $.ajax({
        url: checkapi+'-'+_cnt+'-'+n+'-'+gap,
        dataType: 'json',
        success:function(data){
            if(data.uflag=='0'){ // 未登录/32,96
                _timeFlag = setTimeout(function(){
                    checkScan(n+1);
                }, gap);
            }else{ //  if(data.uflag=='login')
                $('#dtip1').find('.text').html(data.errmsg);
                $('#dtip1').find('.error').hide();
                $('#dtip1').find('.okey').show();
                opToast('dtip1', function(){ 
                    location.reload(); 
                }, 2300);
            }
            _cnt++;
        },
        error:function(err){
            alert('服务器错误，请稍后刷新试一试!');
            //$('#modalMsg').html('服务器错误，请稍后试一试!');
        }
    })
}

function setTab(k){ // act
    clearTimeout(_timeFlag);
    $('.btn-group div').removeClass('btn-act');
    $('#nav_'+k).addClass('btn-act');
    $('.cont').hide();
    if($('#cont_'+k).length==0){
        return;
    }
    $('#cont_'+k).show();
    //return;
    var t1 = k=='wechat', //  && !isWexin
        t2 = k=='wework'; // && !isWework
    if(t1 || t2){
        _timeFlag = setTimeout(function(){
            checkScan(0);
        }, 3700); // (5分钟)300/3.7=81.08
    }else{
        setVcode(k);
    }
}

function setVcode(mod, click){
    var fimg = jsElm.jeID(mod+'_vimg'); 
    var para = "&mod="+mod+"&"+_cbase.safil.url;
    para += "&_r="+(new Date().getTime());
    fimg.setAttribute("src",_cbase.run.fbase+"?ajax-vimg"+para);
}
