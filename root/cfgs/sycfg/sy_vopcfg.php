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
        '/root/run/adm.php',
    ),
    'comm' => array(
        '演示版',
        '/chn.php',
        '?', // [1.屏蔽此行:为动态模式]; [2.设置为`?`:为Pathinfo模式]; [3.设置为`/`:为伪静态模式]; 参考:imcat.txjia.com/book.php/super-rewrite
        //'.htm', // 伪静态后缀
        //'0', // 建议不开启, 伪静态-替换第一层/chn/目录，需要调整伪静态规则
        //array('/home.php','/home.htm'), //  建议为空, 伪静态首页替换；需要增加伪静态规则
    ), 
    'dev' => array(
        '官网发布',
        '/dev.php',
        '?', // [1.屏蔽此行:为动态模式]; [2.设置为`?`:为Pathinfo模式]; [3.设置为`/`:为伪静态模式]
    ), 
    'doc' => array(
        'Released', // Publish
        '/doc.php',
        '?', // [1.屏蔽此行:为动态模式]; [2.设置为`?`:为Pathinfo模式]; [3.设置为`/`:为伪静态模式]
    ), 
    'mob' => array(
        array('cn'=>'手机版', 'en'=>'Mobile'),  
        '/mob.php'
    ), 
    'umc' => array(
        array('cn'=>'会员中心', 'en'=>'User'),
        '/root/run/umc.php'
    ), 
    #'demo' => array('Hello.Demo!','/demo.php'), 
    'base' => array(
        array('cn'=>'默认首页', 'en'=>'Basic'),
        '/index.php', // index,base
    ),
    //'ven' => array('English','/root/run/eng.php'), 
);

// 各模块展示show
$_sy_vopcfg['show'] = array( 
    //'comm' => array('',''), // topic,faqs
    //'doc' => array(''),
    'dev' => array('demo'), 
    'umc' => array('indoc'), 
    //'mob' => array('votes'),
    '_defront_' => 'comm', //默认展示模板
    '_deadmin_' => 'adm', //默认管理模板
    '_hidden_' => array('adminer','inmem'), //无展示模块
);
