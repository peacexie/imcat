<?php
namespace imcat;
$_cbase['run']['subDirs'] = '1';
require __DIR__.'/_config.php'; 

// ?debug,ip,cfgs,html
$qstr = $_SERVER['QUERY_STRING'];

if($qstr=='nav'){
    $rip = '121.'.mt_rand(3,17).'.197.187';
    $data = array('nav','cfgs','html','lang','debug',$rip,$rip.':debug');
    exvFunc::navShow($data,1);
}elseif(in_array($qstr,array('cfgs','html'))){
    exvJump::tab($qstr);
}elseif(in_array($qstr,array('lang'))){
    $dir = exvJump::getLang();
    dump($dir);
}else{
    // ?121.17.197.187, ?121.17.197.187:debug
    exvJump::go($qstr);
}

