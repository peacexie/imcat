<?php
/*
 * user扩展信息模板配置
/*/
$_ve_user = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,close,static
        'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
    ),
    
    'm' => 'user/uinfo',    
    //'home' => 'user/uinfo', //
    'tips' => 'user/tips', //
    
    'uedit' => 'user/uedit', //
    'uedpw' => 'user/uedpw', //
    'mbind' => 'user/mbind', //
    
    'v' => 'tips', //可带view参数
    
    'testlogin' => 'user/test', //
    'testguset' => 'user/test', //
    'testset' => 'user/test', //
    
    'login' => 'user/user_login',
    'apply' => 'user/user_apply',
    //'appdo' => 'c_page/user_acts',
    'getpw' => 'user/user_getpw',
    //'getdo' => 'c_page/user_acts',
    
);
