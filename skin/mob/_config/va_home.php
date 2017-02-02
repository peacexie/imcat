<?php
/*
? rss,wap
? cn,en
*/
$_va_home = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,catch,close
        'stexp' => '2h', //30,60,3h,6h,12h,24h,7d
        'stext' => '.htm',
    ),
    
    //mod.home模块首页模板
    'm' => 'c_page/_home',
    
    //关闭模块
    'close' => array('topic','demo'),
    //文档/资讯:默认按va_docs设置
    //其他未设置模块按关闭处理
    
    //import导入配置的模块
    'imcfg' => array(
        #'demo' => 'news', // demo按news方式显示
        //'gbook' => 'nrem', // gbook按nrem方式显示
    ),
    
    //扩展模块
    'extra' => array('home','info','user'), 
    
);
