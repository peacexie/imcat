<?php
/*
 * 文档通用模板配置
/*/
$_vc_corp = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close
        'stexp' => '2h', //hour(s)
    ),
    
    //mod.home模块首页
    'm' => array(
        '0' => 'corp/mem-list', 
    ), 
    
    //详情页
    //'d' => 'corp/mem-detail',
    'd' => array(
        '0' => 'corp/mem-detail',
        'news' => 'corp/mem-ulst',
        'pro' => 'corp/mem-ulst',
    ),
    
    //类别页
    't' => 'corp/mem-list',

);
