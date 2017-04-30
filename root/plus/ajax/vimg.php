<?php
$_cbase['skip']['error'] = true;
require(dirname(__FILE__).'/_config.php'); 

safComm::urlFrom();
$mod = req('mod');
if(in_array($mod,array('(istest)','(emtel)','qrShow'))){
    //;
}else{
    safComm::urlStamp('check');
}

$tta = array(
    'avant',
    'anty',
    'bvsans',
    'jura',
    'lconsole',
);
//$mod = 'vsms4'; // test

// 字体配置, 字体文件放在：'/static/media/fonts/'目录下
if($mod=='(emtel)'){ // tel,email 
    $tab = req('code'); 
    $tab = comConvert::sysRevert($tab,1);
// 显示表单qr码
}elseif($mod=='qrShow'){ // 二纬码
    $size = req('size','5');
    $data = req('data','','Safe4',255); 
    $level = req('level','2');
    $margin = req('margin','1');
    extQRcode::show($data, $size, $level, $margin);
    die();
}elseif($mod=='vsms4'){ // 短信验证码的验证码,100x40
    $tab = basKeyid::kidRand('0',4); 
    $enc = comConvert::sysBase64($tab); 
    comCookie::oset('vsms4',$enc); 
    $mod = '(emtel)';
}else{ // 验证码
    $tab = '';
}

$ttf = $tta[mt_rand(0,count($tta)-1)];
$vcode = new comVCode($mod, $ttf, $tab);


