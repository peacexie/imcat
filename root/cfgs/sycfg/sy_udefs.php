<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 注册/登录:相关设置

### 登录方式 - 默认用户配置 
### locin, idpwd, mobvc, email, wechat, wework
// 
$_sy_udefs = [

    /* (模拟登录屏蔽此段)
    'locin' => [ // 无配置:存cookie
        'umod' => '', // 模型
        'grade' => 'incom', // 等级
        'show' => '1', // 审核 0,1,''
    ],//*/

    /* (测试:指定模型)
    'locin' => [ // 无配置:存cookie
        'umod' => 'inmem', // 模型
        'grade' => 'incom', // 等级
        'show' => '1', // 审核
    ],//*/

    /* (测试:绑定)
    'locin' => [ // 无配置:存cookie
        'umod' => '',
        'grade' => 'pcom,ccom',
        'show' => '1',
        'umtab' => 'person,company',
    ],//*/

    'wework' => [ // 指定模型:存会员
        'umod' => 'inmem', // 模型
        'grade' => 'incom', // 等级
        'show' => '1', // 审核
    ],

    //*
    'wechat' => [ // 会员模型为空:待绑定
        'umod' => 'person',
        'grade' => 'pcom',
        'show' => '1',
        'umtab' => 'person',
    ],//*/

    /*
    'wechat' => [ // 会员模型为空:待绑定
        'umod' => 'company',
        'grade' => 'ccom',
        'show' => '1',
    ],//*/

    'mobvc' => [ // 会员模型为空:待绑定
        'umod' => 'person',
        'grade' => 'pcom',
        'show' => '1',
    ],

    '_ckss' => [ // 登录方式的cookie-key
        //'_def_' => 'login-uio',
        'eduid' => 'eduid-uio',
    ],

    '_bind' => [ // 会员模型为空:待绑定
        'umod' => 'person',
        'grade' => 'pcom',
        'show' => '1',
    ],

    '_canap' => ',person,organize,', 
    '_debug' => 'Adm1Test,PeaceXie,XieYongShun,TestCS1,ChenGuoQiang,HuangYing,', // 调试页权限 

    '_not_exmod' => ',inmem,',

    // 'tester' => [],
    
];

/*

*/

