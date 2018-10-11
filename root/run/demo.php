<?php
namespace imcat;

//$_cbase['tpl']['tpc_on']  = 1; //是否开启模板缓存，true开启,false不开启 
$_cbase['tpl']['vdir'] = 'demo'; // 指定模板目录
$_cbase['sys']['lang'] = 'cn'; // 指定语言(或使用下一行)
//$_cbase['ucfg']['lang'] = '(auto)'; // (自动)可切换语言
//$_cbase['sys']['skin'] = 'flatly'; // 指定皮肤(或不要默认`min`,或使用下一行)
//$_cbase['ucfg']['skin'] = '(auto)'; // 可切换皮肤, 默认`min`, 
require __DIR__.'/_init.php';  
$vop = new \imcat\vopShow();
