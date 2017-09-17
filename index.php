<?php 
$_cbase['ucfg']['lang'] = '(auto)'; 
require dirname(__FILE__).'/root/run/_init.php';

$qstr = $_SERVER['QUERY_STRING'];
$_cbase['close_home'] = empty($_cbase['close_home']) ? 'index' : $_cbase['close_home'];

if(devRun::prootGet()!=PATH_PROJ){ // 检查路径
    header("Location:./root/tools/adbug/start.php?FixProot"); 
}elseif($qstr=='start'){ //处理start
    header("Location:./root/tools/adbug/start.php?"); 
}elseif(!empty($qstr)){ //处理跳转
    new exvJump();
}elseif($_cbase['close_home']=='close'){
    vopTpls::cinc("_pub:stpl/close_info",0,1);
}elseif(substr($_cbase['close_home'],0,4)=='dir-'){
    $tpl = substr($_cbase['close_home'],4);
    $cfg = read('vopcfg.tpl','sy'); 
    header('Location:'.PATH_PROJ.$cfg[$tpl][1]); 
}else{ //默认页:qstr-空
    // * 原生代码(自己写脚本)
    //require DIR_ROOT.'/tools/rhome/home.php';
    // * 通过模板解析
    $slang = $_cbase['sys']['lang']=='cn' ? 'cn' : 'en';
    vopTpls::cinc("_pub:rhome/home-$slang",0,1);
}
