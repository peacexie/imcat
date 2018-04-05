<?php
//模板显示格式

$_sy_vopcfg = array();

// 所有语言
$_sy_vopcfg['langs'] = array(
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
$_sy_vopcfg['tpl'] = array(
    'adm' => array(
        array('cn'=>'管理中心', 'en'=>'Admin'),
        '/root/run/adm.php'
    ),
    'chn' => array(
        '经典版',  
        '/chn.php',
        //'/', // 伪静态配置：把`.php?` ->替换为 `/`
        //'.htm', // 伪静态后缀
    ), 
    'dev' => array(
        '文档版',  
        '/dev.php',
        //'/', // 伪静态配置：把`.php?` ->替换为 `/`
        //'.htm', // 伪静态后缀
    ), 
    'doc' => array(
        'Manual', 
        '/doc.php',
        //'/', // 伪静态配置：把`.php?` ->替换为 `/`
        //'.htm', // 伪静态后缀
    ), 
    'mob' => array(
        array('cn'=>'手机版', 'en'=>'Mobile'),  
        '/mob.php'
    ), 
    'umc' => array(
        array('cn'=>'会员中心', 'en'=>'User'),
        '/root/run/umc.php'
    ), 
    'rest' => array(
        'REST-API', 
        '/root/run/rest.php'
    ),
    '_pub' => 0,
    /*'app' => array(
        'apiServer', 
        '/root/run/app.php'
    ),*/
    //'demodir' => array('Hello','/root/run/front.php'), 
    //'ven' => array('English','/root/run/eng.php'), 
);

// 各模块展示show
$_sy_vopcfg['show'] = array( 
    //'chn' => array('',''), // topic,faqs
    //'doc' => array(''),
    'dev' => array('demo'), 
    'umc' => array('indoc'), 
    //'mob' => array('votes'),
    '_defront_' => 'chn', //默认展示模板
    '_deadmin_' => 'adm', //默认管理模板
    '_hidden_' => array('adminer','inmem'), //无展示模块
);
