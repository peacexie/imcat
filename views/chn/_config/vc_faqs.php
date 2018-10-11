<?php
/*
 * faqs模板配置
/*/
$_vc_faqs = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,close,static
        'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
        //'tmfix' => '-mob', // 移动适配-模板后缀
    ),
    
    'm' => array(
        '0' => 'faqs/mtype', //首页
        'list' => 'faqs/mtype', //搜索
    ),
    'd' => 'faqs/detail',
    't' => 'faqs/mtype', 
    
    // 类别/栏目:就不要设置如下ID了，否则冲突
    'new' => 'faqs/mtype', //最新
    'tip' => 'faqs/mtype', //精华
    'hot' => 'faqs/mtype', //热门
    'tag' => 'faqs/tags', //标签
    
    //'v' => 'my,dep,pub', //可带view参数 

);
