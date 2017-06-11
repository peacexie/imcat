<?php
$_cbase['skip']['error'] = true;
require dirname(__FILE__).'/_config.php'; 

//safComm::urlFrom();
$mod = req('mod');
if(in_array($mod,array('(istest)','(emtel)','qrShow'))){
    //;
}else{
    safComm::urlStamp('check');
}

$tta['c'] = array(
    //'jura',
    'avant', // -@,均衡
    'bvsans', // 均衡
    'comm',
);
$tta['m'] = array(
    'efjian', // 渐变
    'efm17',
    'efmE',
    'efmO',
);
$tta['u'] = array(
    //'efshou', //斜
    //'kong2',
    //'kong3',
);

$tts1 = 'comm'; // comm:jura(21),bvsans(58)
$ttid = 'c'; // c,m,u:x

// 字体配置, 字体文件放在：'/static/media/fonts/'目录下
if($mod=='(emtel)'){ // tel,email 
    $tab = req('code'); 
    $tab = comConvert::sysRevert($tab,1);
    $ttid = 0;
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
    $ttid = 0;
}else{ // 验证码
    $tab = '';
}

$ttarr = empty($ttid) ? $tts1 : $tta[$ttid];
$vcode = new comVCode($mod, $ttarr, $tab);


