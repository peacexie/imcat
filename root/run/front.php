<?php
define('RUN_FRONT', 1);
//$_cbase['tpl']['tpc_on']  = 1; //是否开启模板缓存，true开启,false不开启 
$_cbase['tpl']['tpl_dir'] = 'demodir';
//$_cbase['sys']['lang'] = 'cn'; // 切换语言
//$_cbase['ucfg']['skin'] = '(auto)'; // 切换皮肤, 默认`min`, 
//$_cbase['sys']['skin'] = 'flatly'; // 固定皮肤
require dirname(__FILE__).'/_init.php';  
$vop = new vopShow();
