<?php
/*
 * 总配置
/*/
$_va_home = array(

    'c' => array(
        'close' => '0', //0,1
        'debug' => '1', //0,1
        'dmacc' => array('127.0.0.1','yscode.txjia.com'),
        'stexp' => '30', //30,60,3h,6h,12h,24h,7d
    ),
    
    //mod.home模块首页模板
    'm' => '',
    
    //关闭模块
    'close' => array('indoc'),
    
    //import导入配置的模块
    'imcfg' => array(
        #'demo' => 'news', // demo按news方式显示
    ),
    
    //扩展模块
    // home,error
    'extra' => array('info','data'), //info-sys,info-read,
    // user-(login,logout,app,del)

);
