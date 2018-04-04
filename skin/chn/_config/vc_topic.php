<?php
/*
 * 文档通用模板配置
/*/
$_vc_topic = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close
        'stexp' => '2h', //hour(s)
    ),
    
    //mod.home模块首页
    'm' => 'u_topic/_index/home', 
    
    //详情页
    'd' => array(
        '0' => 'u_topic/_index/detail',
        'v' => 'u_topic/_index/detail',
    ),
    
    //类别页
    't' => 'u_topic/_index/home',

);
