<?php
/*
 * 总配置
/*/
$_va_home = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,catch,close
        '_defCtrl' => '_defCtrl', 
        /*
        'close' => '0', //0,1
        'debug' => '1', //0,1
        'dmacc' => array('127.0.0.1','yscode.txjia.com'),
        'stexp' => '30', //30,60,3h,6h,12h,24h,7d
        */
        'close' => array(),
        'imcfg' => array(),
        'extra' => array('home'),
    ),

    //mod.home模块首页模板
    'm' => 'home/mhome',
    'error' => 'home/error',
    'token' => 'home/token',

);
