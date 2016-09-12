<?php
define('RUN_FRONT', 1);
//$_cbase['tpl']['tpc_on']  = 1; //是否开启模板缓存，true开启,false不开启 
$_cbase['tpl']['tpl_dir'] = 'demodir';
//$_cbase['sys']['lang'] = 'cn'; // 切换语言
require('./_paths.php');  
$vop = new vopShow(); 