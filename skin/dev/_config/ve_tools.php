<?php
(!defined('RUN_INIT')) && die('No Init');
/*
 * tools扩展信息模板配置
/*/
$_ve_tools = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,close,static
        'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
    ),
    'm' => 'tools/b_home',
    //'index' => 'tools/b_index',

);

include dirname(dirname(__FILE__)).'/tools/a_cfgs.php';
foreach ($cfgs as $key => $v) {
    $_ve_tools[$key] = 'tools/tools_'.$key;
}
