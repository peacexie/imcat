<?php
/*
*/
$_vc_about = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,close,static
        'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
        'stypes' => array('afqas','anews','aserv','apics'), //内容页生产静态的栏目,为空则不限制
    ),
    'm' => 'first',
    'd' => 'm_about/about_detail',
    't' => 'm_about/about_page', //默认模版:about,alink
    
    'profile' => array( //公司简介  
        '0' => 'm_about/about_page',
    ),
    'awhua' => array( //企业文化
        '0' => 'm_about/about_page',
        'v2' => 'm_about/about_page',
        'v3' => 'm_about/about_page',
    ),
    'anews' => 'm_about/about_list', //公司新闻
    'apics' => 'm_about/about_pics', //公司图片
    'aserv' => 'm_about/about_serv', //服务内容
    'afqas' => 'm_about/about_fqas', //常见问题
    'alink' => array( //联系我们
        '0' => 'm_about/about_link',
        'v2' => 'm_about/about_link',
        'v3' => 'm_about/about_link',
    ),

    //'v' => 'v1,v2,v3', //所有类别可带view参数
    
    /*'d' => array(
        '0' => 'm_about/about_detail',
        'list1' => 'm_about/about_dlist1',
        'list2' => 'm_about/about_dlist2',
    )*/

);
