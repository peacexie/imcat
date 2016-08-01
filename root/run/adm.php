<?php
define('RUN_ADMIN', 1);
$_cbase['tpl']['tpc_on']  = 1; //是否开启模板缓存，true开启,false不开启 
$_cbase['tpl']['tpl_dir'] = 'adm';
require('./_paths.php');
$vop = new vopShow(0);
$vop->run(usrAdmin::getMkv());