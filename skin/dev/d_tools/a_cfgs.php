<?php
(!defined('RUN_INIT')) && die('No Init');

$cfgs = array(

    'bezier' => array(
        'title' => '贝塞尔曲线 (Bezier Curve) ',
        'rem' => '贝塞尔曲线是计算机图形学中相当重要的参数曲线，在一些比较成熟的位图软件中也有贝塞尔曲线工具，如PhotoShop等。',
        'mt' => '贝塞尔曲线',
    ),

    'schulte' => array(
        'title' => '舒尔特方格 (Schulte Grid) ',
        'rem' => '在一张方形卡片上画上25 个方格，格子内任意填写上阿拉伯数字 1 ~ 25 等共 25 个数字；测试幼儿注意力水平。',
        'mt' => '舒尔特方格',
    ),

    'ipaddr' => array(
        'title' => 'IP转化地址类（IPv4）',
        'rem' => '接口：Local(本地纯真IP数据库)，Sina，Pcoln(太平洋接口)，Taobao(淘宝接口)，Ip138，S1616',
        'mt' => 'IP转地址类',
    ),
    
    'cnconv' => array(
        'title' => '简繁转化（js/php）',
        'rem' => '简繁转化接口',
        'mt' => '简繁转化',
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
    
    'chrcom' => array(
        'title' => '常用字符集',
        'rem' => '按类别，整理收集常用字符集',
        'mt' => '常用字符集',
    ),
    
    'chrall' => array(
        'title' => 'FFFF全字符集',
        'rem' => '0000~FFFF，共65536个全字符集',
        'mt' => 'FFFF全字符集',
    ),    
    
);

/*
top域名, 常用字符集
FFFF全字符集

安全专题.随机表单.Url签名, 
*/

//用于首页对齐的链接
$cfgs_ext = array(
    'dedecms' => array(
        'mt' => 'DEDE织梦(CMS)',
        'url' => 'http://dedecms.com/',
    ),
    'destoon' => array(
        'mt' => 'Destoon(B2B)',
        'url' => 'http://www.destoon.com/product/',
    ),
);

/*
    'kindsoft' => array(
        'mt' => 'KindEditor',
        'url' => 'http://www.kindsoft.net/down.php',
    ),
    'ueditor' => array(
        'mt' => 'UEditor',
        'url' => 'http://ueditor.baidu.com/',
    ),
*/

