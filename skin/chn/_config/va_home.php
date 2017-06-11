<?php
/*
 * 首页模板和通用配置
/*/
$_va_home = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close(关闭)
        'stext' => '.html', 
        'stexp' => '2h', //hour(s)
        //'tmfix' => '-mob', // 移动适配-模板后缀(正式使用请丰富模板,或屏蔽这里)
    ),
    
    //mod.home模块首页模板
    'm' => 'c_page/_home',
    
    //关闭模块
    'close' => array('indoc','votes'),
    //文档/资讯:默认按va_docs设置
    //其他未设置模块按关闭处理
    
    //import导入配置的模块
    'imcfg' => array(
        //'gbook' => 'nrem', // gbook按nrem方式显示
        'crem' => 'nrem', 
        'trem' => 'nrem',
        'kerem' => 'nrem',
        'drem' => 'nrem',
        //'company' => 'company', 
        'govern' => 'company', 
        'organize' => 'company', 
    ),
    
    //扩展模块
    'extra' => array('home','info','type','ocar'), 
    
);
