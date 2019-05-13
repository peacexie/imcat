<?php
/*
*/
$_vc_about = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,close,static
        'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
        //'stypes' => array('afqas','anews','aserv','apics'), //内容页生产静态的栏目,为空则不限制
    ),
    'm' => 'first',
    'd' => 'about/about-detail',
    't' => 'about/about-page', //默认模版:about,alink
    
    'profile' => array( //公司简介  
        '0' => 'about/about-page',
    ),
    'awhua' => array( //企业文化
        '0' => 'about/about-page',
        'v2' => 'about/about-page',
        'v3' => 'about/about-page',
    ),
    'anews' => 'about/about-list', //公司新闻
    'apics' => 'about/about-pics', //公司图片
    'aserv' => 'about/about-serv', //服务内容
    'afqas' => 'about/about-fqas', //常见问题
    'alink' => array( //联系我们
        '0' => 'about/about-link',
        'v2' => 'about/about-link',
        'v3' => 'about/about-link',
    ),

    //'v' => 'v1,v2,v3', //所有类别可带view参数
    
    /*'d' => array(
        '0' => 'about/about-detail',
        'list1' => 'about/about-dlist1',
        'list2' => 'about/about-dlist2',
    )*/

);
