<?php
define('RUN_UMC', 1);
//$_cbase['tpl']['tpc_on']  = 0;
$_cbase['tpl']['tpl_dir'] = 'umc';
$_cbase['ucfg']['lang'] = '(auto)'; 
// 路由简化配置
$_cbase['route'] = array( 
    'reg' => 'home-apply',
); 
require dirname(__FILE__).'/_init.php'; 
$vop = new vopShow();
