<?php
define('RUN_FRONT', 1);
$_cbase['tpl']['tpc_on']  = true; //是否开启模板缓存，true开启,false不开启 
$_cbase['tpl']['tpl_dir'] = 'chn';
//$_cbase['sys']['lang'] = 'cn'; // 切换语言
require(dirname(__FILE__).'/root/run/_paths.php'); 
$vop = new vopShow(); 