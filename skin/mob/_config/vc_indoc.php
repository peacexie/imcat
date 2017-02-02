<?php
/*
 * indoc模板配置
/*/
$_vc_indoc = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,close,static
        'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
    ),
    
    'm' => 'indoc/main',
    'd' => 'indoc/detail',
    //'t' => 'indoc/list', //my
    
    /*
    'iget' => 'indoc/list', //my,dep,pub
    'iadm' => 'indoc/uadm', 
    'iedit' => 'indoc/uedit',
    */

    #'v' => 'my,dep,pub', //可带view参数 

);
