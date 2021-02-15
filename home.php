<?php
define('RUN_FRONT', 1);
//$_cbase['tpl']['tpc_on']  = 0; //是否开启模板缓存
$_cbase['tpl']['vdir'] = 'comm';
$_cbase['sys']['lang'] = 'cn'; // 切换语言
#$_cbase['tpl']['bdpart'] = 'cn'; // 绑定[语言/城市]资料
require __DIR__.'/root/run/_init.php'; 
$vop = new \imcat\vopShow();
