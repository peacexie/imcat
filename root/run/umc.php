<?php
define('RUN_UMC', 1);
//$_cbase['tpl']['tpc_on']  = 0;
$_cbase['tpl']['vdir'] = 'umc';
$_cbase['ucfg']['lang'] = '(auto)'; 
require __DIR__.'/_init.php'; 
$vop = new \imcat\vopShow();
