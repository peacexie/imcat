<?php
(!defined('RUN_INIT')) && die('No Init');

$cfgs = array(

    'qrorc' => array(
        'title' => '识别二维码/识别文字',
        'rem' => '识别二维码内容,识别图片文字内容',
        'mt' => '识文识码',
    ),

    'ipaddr' => array(
        'title' => 'IP转化地址类（IPv4）',
        'rem' => '接口：Local(本地纯真IP数据库)，Sina，Pcoln(太平洋接口)，Taobao(淘宝接口)，Ip138，S1616',
        'mt' => 'IP转地址类',
    ),
    
    'cnconv' => array(
        'title' => '简繁/拼音转化（js/php）',
        'rem' => '简繁转化接口,拼音转化接口',
        'mt' => '简繁/拼音转化',
    ),

    'seal' => array(
        'title' => 'PHP印章制作DIY',
        'rem' => 'PHP印章在线制作，用于在线办公，电子签名等场合',
        'mt' => 'PHP印章制作',
    ),
    
    'qrcode' => array(
        'title' => '二维码生成器（QRCODE）',
        'rem' => '二维码生成器，二维码在线制作',
        'mt' => '二维码生成器',
    ),
    
    'vimg' => array(
        'title' => '电话/邮箱:敏感信息图片显示',
        'rem' => '电话/邮箱:敏感信息图片显示，认证码显示',
        'mt' => '电话图片显示',
    ),
    
    'shapan' => array(
        'title' => '沙盘-图片中显示自定义沙盘点',
        'rem' => '沙盘，在图片(地图)中显示热点(自定义沙盘)坐标点',
        'mt' => '地图中的沙盘',
    ),
    
    'spword' => array(
        'title' => '中文分词-关键词提取',
        'rem' => '中文分词，关键词提取',
        'mt' => '中文分词',
    ),
    
    'wmark' => array(
        'title' => '图片水印，文字描边水印',
        'rem' => '图片水印，文字描边水印',
        'mt' => '水印描边',
    ),

);

//用于首页对齐的链接
$cfgs_ext = array(

    'bezier' => array(
        'mt' => 'Node.js微框架',
        'url' => 'http://txjia.com/peace/wenode.htm',
    ),

    'schulte' => array(
        'mt' => '近7天地震',
        'url' => 'http://imcat.txjia.com/root/plus/yscode/dizhen.php',
    ),

);
