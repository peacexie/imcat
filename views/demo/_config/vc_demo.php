<?php
/*
 * 文档通用模板配置
/*/
$_vc_demo = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close
        'stexp' => '2h', //hour(s)
    ),
    
    //mod.home模块首页
    'm' => array(
        '0' => 'news/mhome', //首页(key=0,first; val=list,home
        'list' => 'news/mtype', //搜索
    ), 
    
    //详情页
    'd' => 'news/detail',
    
    //类别页
    't' => 'news/mtype',
    
    //单个类别(模板)
    #'serv' => 'c_mod/{mod}_serv', //服务内容

);
