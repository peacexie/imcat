<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 注册/登录:相关设置

### (本地)登录测试 - 指定用户
$_sy_udemo = [
    'null' => [
        'ckey' => '2020-xx-guest',
        'utype' => '(guest)',
        //'uid' => 'locdemo',
        'uname' => '(游客)',
        'grade' => '0',
        'upic' => PATH_STATIC.'/icons/basic/demo_60x60.gif',
    ],
    'locdemo' => [
        'ckey' => '2020-bm-6688',
        'utype' => 'loctest',
        //'uid' => 'locdemo',
        'uname' => '测试和平鸽',
        'grade' => '1',
        'upic' => 'https://imcat.txjia.com/ximps/static/media/collect/wiki_02-160x120.gif',
    ],
    'demopeace' => [
        'ckey' => '2020-bm-8899',
        'utype' => 'loctest',
        //'uid' => 'locdemo',
        'uname' => '测试永顺',
        'grade' => '1',
        'upic' => 'https://imcat.txjia.com/ximps/static/media/collect/qiezi_09.jpg',
    ],
    'tester' => [
        //
    ],
];


