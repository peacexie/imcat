<?php
/*
 * 文档通用模板配置
/*/
$_vc_keres = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close
        'stexp' => '2h', //hour(s)
    ),
    
    //mod.home模块首页
    'm' => array(
        '0' => 'news/mhome', //首页,news/mtype
        'list' => 'news/keres-list', //搜索,news/mtype
    ), 
    
    //详情页
    'd' => 'news/keres-detail',
    
    //类别页
    't' => 'news/keres-list',

);
