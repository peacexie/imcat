<?php 
$_cbase['ucfg']['lang'] = '(auto)'; 
require dirname(__FILE__).'/root/run/_init.php';

$qstr = $_SERVER['QUERY_STRING'];
$_cbase['close_home'] = empty($_cbase['close_home']) ? 'index' : $_cbase['close_home'];
//$_cbase['close_home'] = 'close'; // close,

if(devRun::prootGet()!=PATH_PROJ){ // 检查路径
    header("Location:./root/tools/adbug/start.php?FixProot"); 
}elseif($qstr=='start'){ //处理start
    header("Location:./root/tools/adbug/start.php?"); 
}elseif(!empty($qstr) && !is_numeric($qstr)){ //处理跳转
    require DIR_ROOT.'/plus/ajax/redir.php';
}elseif($_cbase['close_home']=='close'){
    vopShow::inc("_pub:stpl/close_info",0,1);
}elseif(substr($_cbase['close_home'],0,4)=='dir-'){
    $tpl = substr($_cbase['close_home'],4);
    $cfg = read('vopcfg.tpl','sy');
    $dir = PATH_PROJ.$cfg[$tpl][1]; 
    header('Location:'.$dir); 
}else{ //默认页:index/qstr-空/qstr-数字
    // * 原生代码(自己写脚本)
    //require DIR_ROOT.'/tools/rhome/home.php';
    // * 通过模板解析
    vopShow::inc("_pub:rhome/home",0,1);
}
