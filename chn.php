<?php
define('RUN_FRONT', 1);
//$_cbase['tpl']['tpc_on']  = 0; //是否开启模板缓存
$_cbase['tpl']['tpl_dir'] = 'chn';
$_cbase['sys']['lang'] = 'cn'; // 切换语言
require dirname(__FILE__).'/root/run/_init.php'; 
$vop = new vopShow();
