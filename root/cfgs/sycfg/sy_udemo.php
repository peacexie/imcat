<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 注册/登录:相关设置

### (本地)登录测试 - 指定用户
// 
$_sy_udemo = [

    'null' => [
        'umod' => 'person',
        'uname' => '(guest)',
        'mname' => '(游客)',
        'mpic' => PATH_STATIC.'/icons/basic/demo_60x60.gif',
    ],

    'locdemo' => [
        'uname' => 'locdemo',
        'mname' => '测试Demo',
        'mpic' => 'http://txjia.com/share_ximps/static/media/collect/wiki_02-160x120.gif',
    ],

    'demoxys' => [
        'uname' => 'demoxys',
        'mname' => '测试永顺',
        'mpic' => 'http://txjia.com/share_ximps/static/media/collect/qiezi_09.jpg',
    ],

    'XieYongShun' => [
        'pptuid' => 'XieYongShun',
        'mname' => '谢永顺',
    ],

    'LiHaoYu' => [
        'pptuid' => 'LiHaoYu',
        'mname' => '李浩宇',
    ],

    'wetest' => [
        'pptuid' => 'oyDK8vjjcn2cFbxMLaMB_8899_Ck',
        'mext' => 'sex=1',
        //'uname' => 'demopeace',
        'mname' => '微信测试者',
        'mpic' => 'http://txjia.com/share_ximps/static/media/collect/qiezi_09.jpg',
    ],

    /*
    $ext = "sex={$user['sex']}".(empty($user['unionid']) ? '' : ";unionid={$user['unionid']}");
    $utmp = ['pptuid'=>$user['openid'], 'mname'=>$user['nickname'], 'mpic'=>$user['headimgurl'], 'mext'=>$ext];
    $urow = $utmp + $this->rlog;
    $this->saveLogin($urow, 'wechat');
    */

    // 'tester' => [],
    
];


