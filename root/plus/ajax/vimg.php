<?php
$_cbase['skip']['error'] = true;
require(dirname(__FILE__).'/_config.php'); 

safComm::urlFrom();
$mod = req('mod');
if(in_array($mod,array('(istest)','(emtel)','qrShow','qrVstr','qrVauto','qrVres'))){
    //;
}else{
    safComm::urlStamp('check');
}

// - 验证码
// - tel,email
// - 二纬码

// 字体配置, 字体文件放在：'/static/media/fonts/'目录下
if($mod=='(emtel)'){
    $tta = array(
        //'anty',
        'jura',
        'lconsole',
    );
    $tab = req('code'); //empty($_cbase['ucfg']['vimg']) ? 'k' : $_cbase['ucfg']['vimg']; // 0,h,H,k
    $tab = comConvert::sysRevert($tab,1);
    //$tab = urldecode($tab); //'peace_xie@08-CMS.com';
// 显示表单qr码
}elseif($mod=='qrShow'){
    $size = req('size','5');
    $data = req('data','','Safe4',255); 
    $level = req('level','2');
    $margin = req('margin','1');
    extQRcode::show($data, $size, $level, $margin);
    die();
    
}elseif(in_array($mod,array('qrVstr','qrVauto','qrVres'))){
    $vqr = new extQRform();
    $res = $vqr->$mod();
    if($mod=='qrVres'){
        $flag = $res[0];
        glbHtml::page(lang('plus.vimp_res'),'1'); 
        glbHtml::page('body');
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $msg = '<p>'.($flag=='OK' ? lang('plus.vimp_ok') : lang('plus.vimp_err')."<br>$flag").'</p>';
        echo "<p>$msg</p>";
        foreach($res[1] as $k=>$v){ echo "<br>$k=".@$v; }
        dump($_GET,1);
        glbHtml::page('end');
    }else{
        die($res);
    }
}else{
    $tta = array(
        'avant',
        'anty',
        'bvsans',
        'jura',
        'lconsole',
    );
    //usleep(200000); //暂停200毫秒，防注册机，发帖机爆力破解...
    $tab = '';
}

$ttf = $tta[mt_rand(0,count($tta)-1)];
$vcode = new comVCode($mod, $ttf, $tab);


