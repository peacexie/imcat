<?php
require dirname(__FILE__).'/_config.php';
glbHtml::head('html');
// 邮箱,短信发送接口

$ucfg = read('user','sy'); 
//die("success");

$mod = req('mod');
$tel = req('tel');
$code = req('code');

// 发短信-验证码
if($mod=='sms-vcode' && $ucfg['regnow']=='sms-vcode' && $code && $tel){
    safComm::urlFrom();
    safComm::urlStamp('check',480); // 8min(5min)
    $vcres = safComm::formCVimg('vsms4', $code, 'check', 480);
    if($vcres) die("var ajres='验证码错误！';"); // $vcres
    // check 发送频率
    $sms = new extSms();
    $tpl = $ucfg['utpls'][$mod];
    $code = basKeyid::kidRand('0',6);
    $re = $sms->sendTpl($tel,$tpl,array('code'=>$code),1,array('pid'=>"$mod:$code"));
    if($re[0]==1) die("var ajres='success';");
    else die("var ajres='{$re[1]}';");
//}elseif($mod=='mail-act'){
    //
}else{
    die("var ajres='Unknow Error!';");
    //die("var _repeat_res = '$msg';");
}

