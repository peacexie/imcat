<?php
define('RUN_ADMIN', 1);
//$_cbase['tpl']['tpc_on']  = 0; //是否开启模板缓存
$_cbase['tpl']['tpl_dir'] = 'adm';
$_cbase['ucfg']['lang'] = '(auto)'; 
// 路由简化配置
$_cbase['rmcfg'] = array( 
    'amain'  => 'home-amain',
    'awtop'  => 'home-awtop',
    'uhome'  => 'home-uhome',
    'login'  => 'home-login',
    'logout' => 'home-logout',
    'help'   => 'home-help',
); 
require(dirname(__FILE__).'/_init.php');
$vop = new vopShow(0);
$vop->run(usrAdmin::getMkv());