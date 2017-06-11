<?php
define('RUN_APP', 1);
//$_cbase['tpl']['tpc_on']  = 1; //是否开启模板缓存，true开启,false不开启 
$_cbase['tpl']['tpl_dir'] = 'app';
//$_cbase['sys']['lang'] = 'cn'; // 切换语言
require dirname(__FILE__).'/_init.php';
vopTpls::pinc('tex_main');
$vop = new tex_main();
