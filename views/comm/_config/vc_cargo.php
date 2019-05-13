<?php
/*
 * cargo模板配置
/*/
$_vc_cargo = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close
        'stexp' => '2h', //hour(s)
    ),
    
    //mod.home模块首页
    'm' => array(
        '0' => 'cargo/cargo-list', //首页(key=0,first; val=list,home
        'list' => 'cargo/cargo-list', //搜索
    ), 
    
    //详情页
    'd' => 'cargo/cargo-detail',
    
    //类别页
    't' => 'cargo/cargo-list',
    
    //单个类别(模板)
    #'serv' => 'c_mod/{mod}_serv', //服务内容

);
