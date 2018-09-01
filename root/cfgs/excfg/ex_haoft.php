<?php

$_ex_haoft = array(
    // 城市配置
    'linzhi' => array(
        'ak' => 'BKC4',
        'as' => '2a849665550f464eb4db90b3f4939a4a',
    ),
    'chenzhou' => array(
        'ak' => 'BA12', // 测试
        'as' => '1234...abcd',
    ),
    'zh' => array( // 测试
        'ak' => 'BADG',
        'as' => 'abcd...1234',
    ),
    // 全局配置
    'api-colse' => 0,
    'db-key' => 'imhouse', // db配置, 为空则共用数据库
    'def-city' => 'linzhi', // 默认城市
);

include __DIR__.'/ex_haoft-fields.php';
