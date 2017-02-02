<?php
/*
 * 文档通用模板配置
/*/
$_va_docs = array(

    //config配置
    'c' => array(
        'vmode' => 'close', //dynamic,static,close
        'stexp' => '2h', //hour(s)
    ),
    
    //mod.home模块首页
    'm' => array(
        '0' => 'c_mod/news_home', //首页(key=0,first; val=list,home
        'list' => 'c_mod/news_list', //搜索
    ), 
    
    //详情页
    'd' => 'c_mod/news_detail',
    
    //类别页
    't' => 'c_mod/news_list',
    
    //单个类别(模板)
    #'serv' => 'c_mod/{mod}_serv', //服务内容

);
