<?php
define('RUN_APIV', 1);
//$_cbase['tpl']['tpc_on']  = 0; //是否开启模板缓存
$_cbase['tpl']['vdir'] = 'sapi';
//$_cbase['ucfg']['lang'] = '(auto)'; 
require dirname(__DIR__).'/run/_init.php';

$vop = new \imcat\vopSapi();
