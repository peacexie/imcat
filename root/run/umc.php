<?php
define('RUN_UMC', 1);
//$_cbase['tpl']['tpc_on']  = 0;
$_cbase['tpl']['tpl_dir'] = 'umc';
$_cbase['ucfg']['lang'] = '(auto)'; 
// 路由简化配置
$_cbase['rmcfg'] = array( 
    'reg'    => 'home-apply',
    'login'  => 'home-login',
    'logout' => 'home-logout',
); 
require(dirname(__FILE__).'/_init.php'); 
$vop = new vopShow();