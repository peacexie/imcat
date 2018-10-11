<?php
(!defined('RUN_INIT')) && die('No Init');
/*
 * extend扩展信息模板配置
/*/
$_ve_extend = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,close,static
        'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
    ),

);

include dirname(__DIR__).'/extend/cfgs.php';
foreach ($cfgs as $key) {
    $_ve_extend[$key] = 'extend/mtype';
}
