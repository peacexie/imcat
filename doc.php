<?php
define('RUN_DEV', 1);
//$_cbase['tpl']['tpc_on']  = 0;
$_cbase['tpl']['vdir'] = 'doc';
$_cbase['sys']['lang'] = 'en'; // 切换语言
require __DIR__.'/root/run/_init.php';
$vop = new \imcat\vopShow();
