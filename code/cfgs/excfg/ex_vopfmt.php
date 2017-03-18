<?php
//模板显示格式

$_ex_vopfmt = array();

// 所有语言
$_ex_vopfmt['langs'] = array(
    'en' => array(
        'English',  
        'En'
    ),
    'cn' => array(
        '中文版',  
        '中'
    ), 
);

// 所有模板
$_ex_vopfmt['tpl'] = array(
    'adm' => array(
        array('cn'=>'管理中心', 'en'=>'Admin'),
        '/root/run/adm.php'
    ),
    'chn' => array(
        '经典PC版',  
        '/chn.php'
    ), 
    'dev' => array(
        '中文文档',  
        '/dev.php'
    ), 
    'doc' => array(
        'Guides',  
        '/doc.php'
    ), 
    'mob' => array(
        array('cn'=>'手机版', 'en'=>'Mobile'),  
        '/mob.php'
    ), 
    'umc' => array(
        array('cn'=>'会员中心', 'en'=>'User'),
        '/root/run/umc.php'
    ), 
    'app' => array(
        'AppServer', 
        '/root/run/app.php'
    ),
    //'demodir' => array('hello','/root/run/front.php'), 
);

// 各模块展示show
$_ex_vopfmt['show'] = array( 
    //'chn' => array('',''), 
    'dev' => array('demo'), 
    'umc' => array('indoc','faqs'), 
    'mob' => array('votes'),
    '_defront_' => 'chn', //默认展示模板
    '_deadmin_' => 'adm', //默认管理模板
    '_hidden_' => array('adminer','inmem'), //无展示模块
);
