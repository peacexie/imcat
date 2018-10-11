
// 135-6789-1234
var maxWait60 = 20; // 重发等待秒数
var canResend = 0;
var isOpened = 0;

// 
function chkMtel(){
    if(jsElm.jeID('secs_60')) return;
    if(isOpened) return; // 只执行一次
    var tel = $('#fm_mtel').val();
    var treg = /^((1[0-9]{2})+(([-]{0,1})+\d{4}){2})$/; 
    if(!treg.test(tel)){
        $('#send2nd').html(lang('umc.input_mobmum'));
        $('#rowvc1').hide();
    }else{
        $('#send2nd').html(lang('umc.wait_send'));
        $('#rowvc1').show();
        isOpened = 1;
    } 
}

function sendSms(){
    var vc = $('#rowvc1').find("input[type='text']").val(); 
    if(/^(\d{4})$/.test(vc)){ 
        // ajax-send
        var para = 'mod=sms-vcode&tel='+$('#fm_mtel').val()+'&code='+vc;
            para += '&'+_cbase.safil.url+'&'+jsRnd();
        jQuery.getScript(_cbase.run.fbase+'?ajax-vcsms&'+para,function(){ 
            if(ajres=='success'){
                $('#send2nd').html("<b id='secs_60'>"+maxWait60+"</b>"+lang('umc.send_again'));
                $('#rowvc1').hide(); 
                setTimeout('chkTimes()',1000); 
                $('#fm_mtel').attr("readonly","readonly"); //removeAttr  
                layer.tips(lang('umc.send_ok'), '#fm_mtel');
                canResend = 0;
            }else{
                layer.tips(ajres, '#fm_mtel');
                $('#send2nd').html(lang('umc.can_resend'));
            }
        });
    }else{
        alert(lang('umc.input_smscode'));
    }
}

function reSend(){
    if(jsElm.jeID('secs_60')) return;
    if(canResend) $('#rowvc1').show();
    $('#rowvc1').find("input[type='text']").val(''); 
    fsCode("vsms4","reLoad");
}

function chkTimes(){
    var sec = parseInt($('#secs_60').html());
    if(sec>0){
        $('#secs_60').html(sec-1);
        setTimeout('chkTimes()',1000);
    }else{
        $('#send2nd').html(lang('umc.can_resend'));
        $('#fm_mtel').removeAttr("readonly"); 
        canResend = 1;
    }
}

