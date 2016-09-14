<?php
define('RUN_ADMIN', 1);
$_cbase['tpl']['tpc_on']  = 0; //是否开启模板缓存
$_cbase['tpl']['tpl_dir'] = 'adm';
$_cbase['ucfg']['lang'] = '(auto)'; 
require('./_paths.php');
$vop = new vopShow(0);
$vop->run(usrAdmin::getMkv());