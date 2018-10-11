<?php
define('RUN_FRONT', 1);
//$_cbase['tpl']['tpc_on']  = 0; //是否开启模板缓存
$_cbase['tpl']['vdir'] = 'base';
$_cbase['ucfg']['lang'] = '(auto)'; // 切换语言
require __DIR__.'/root/run/_init.php'; 
$vop = new \imcat\vopShow();
