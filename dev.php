<?php
define('RUN_DEV', 1);
$_cbase['tpl']['tpl_dir'] = 'dev';
//$_cbase['sys']['lang'] = 'cn'; // 切换语言
require(dirname(__FILE__).'/root/run/_paths.php'); 
$vop = new vopShow();