<?php
define('RUN_MOB', 1);
//$_cbase['tpl']['tpc_on']  = 0;
$_cbase['tpl']['tpl_dir'] = 'mob';
//$_cbase['sys']['lang'] = 'en'; // 切换语言
require dirname(__FILE__).'/root/run/_init.php'; 
$vop = new \imcat\vopShow();
