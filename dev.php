<?php
define('RUN_DEV', 1);
//$_cbase['tpl']['tpc_on']  = 0;
$_cbase['tpl']['vdir'] = 'dev';
$_cbase['sys']['lang'] = 'cn'; // 切换语言
require __DIR__.'/root/run/_init.php'; 
$vop = new \imcat\vopShow();
