<?php
/*
 * 首页模板和通用配置
/*/
$_va_home = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close(关闭)
        '_defCtrl' => '_defCtrl', 
        'stext' => '.html', 
        'stexp' => '2h', //hour(s)
        //'tmfix' => '-mob', // 移动适配-模板后缀(正式使用请丰富模板,或屏蔽这里)
        'imcfg' => array(),
        'extra' => array(), // 扩展模块
    ),
    
    //mod.home模块首页模板
    'm' => 'home/en', // cn|en

    'start' => '(jump)',
    //'update' => 'home/update',
  
);
