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
        'imcfg' => array( // import导入配置的模块
            //'gbook' => 'nrem', // gbook按nrem方式显示
            'crem' => 'nrem', 
            'trem' => 'nrem',
            'kerem' => 'nrem',
            'drem' => 'nrem',
            'company' => 'corp', 
            'govern' => 'corp', 
            'organize' => 'corp', 
        ),
        'extra' => array('type','ocar'), // 扩展模块:'home','info','type','ocar'
    ),
    
    //mod.home模块首页模板
    'm' => 'home/index',
  
);
