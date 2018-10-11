<?php
define('RUN_ADMIN', 1);
//$_cbase['tpl']['tpc_on']  = 0; //是否开启模板缓存
$_cbase['tpl']['vdir'] = 'adm';
$_cbase['ucfg']['lang'] = '(auto)'; 
require __DIR__.'/_init.php';
$vop = new \imcat\vopShow();
