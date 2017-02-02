<?php
/*
 * 文档通用模板配置
/*/
$_vc_company = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close
        'stexp' => '2h', //hour(s)
    ),
    
    //mod.home模块首页
    'm' => array(
        '0' => 'c_mod/mem_list', 
    ), 
    
    //详情页
    //'d' => 'c_mod/mem_detail',
    'd' => array(
        '0' => 'c_mod/mem_detail',
        'news' => 'c_mod/mem_ulst',
        'pro' => 'c_mod/mem_ulst',
    ),
    
    //类别页
    't' => 'c_mod/mem_list',

);
