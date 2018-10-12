<?php

$_ex_haoft = array(
    // 城市配置
    'linzhi' => array(
        'ak' => 'B?88', // 接口`COMP_NO`，如`BC12`
        'as' => '', // 接口`SYNC_VERIFYID`，32位字符
    ),
    'chenzhou' => array(
        'ak' => 'BA12', // 测试
        'as' => '1234...abcd',
    ),
    // 全局配置
    'api-open' => 0,
    'db-key' => 'imhaoft', // db配置, 为空则共用数据库
    'def-city' => 'linzhi', // 默认城市
);

include __DIR__.'/ex_haoft-fields.php';
include __DIR__.'/ex_haoft-price.php';
