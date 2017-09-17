<?php
/*
? 
*/
$_va_home = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,catch,close
        'stexp' => '2h', //30,60,3h,6h,12h,24h,7d
        'stext' => '-u.htm', 
        '_tabCtrl' => array('homeCtrl','userCtrl','uioCtrl'),
        //'_defCtrl' => '_defCtrl', 
        'pskip' => array('home','faqs','uio',), //umc-不需要登录模型
        'close' => array(),
        'imcfg' => array(),
        'extra' => array('user','order','uio'),
    ),
    
    //mod.home模块首页模板
    'm' => 'user/home',
    
);
    