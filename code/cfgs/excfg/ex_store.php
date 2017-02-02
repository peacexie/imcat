<?php

// 附件模式: rsLocal,rsFtp,rsSea
$_cfg['type'] = 'rsLocal'; 

// 缩略图大小
$_cfg['resize'] = ';240x180,160x120,120x90,120x60;88x31,40x40,120x120;'; 
$_cfg['resize'] .= '180x240,120x160,90x120,60x120;31x88,80x80,240x240;'; 

// Local附件-默认,不用配置
$_cfg['rsLocal'] = array(
    //
);

// FTP附件
$_cfg['rsFtp'] = array(
    'ftp_ssl'     => false, // true,false
    'hostname'    => 'host-ip',
    'username'    => 'ftp-userid',
    'password'    => 'ftp.passwd',
    'port'        => 21,
    'passive'     => true,
    'debug'       => true,
    'dir_ures'    => '/www/imcat', // ftp根路径
    // 缩略图剪切地址,可自行设置重定向(如:/cut/(size)/(img))
    'cut_ures'    => 'http://domain.com/cut.php?size=(size)&img=(img)',
);

// Sea附件
$_cfg['rsSea'] = array(
    //
);

$_ex_store = $_cfg; 

/*

### FTP附件 配置说明

* 配置ftp服务器
 - 自行配置好ftp服务器，并把子域名指向相关目录
 - 示例结果: http://domain.com/imcat/cargo/2016-cq/n6q1/ma2s8.jpg  
 - url对应的ftp文件: /www/imcat/cargo/2016-cq/n6q1/ma2s8.jpg
 - 为了优化,imcat目录可去掉

* 配置系统参数
 - ftp相关参数，配置到以上 $_cfg['rsFtp'] 区块
 - 配置 _paths.php 中设置 PATH_URES ：(注意: DIR_URES 设置按本地设置)
 - define('PATH_URES', 'http://domain.com/imcat');

* 缩略图优化
 - 各服务器上，自行设置
 - 参考：/root/plus/api/thumb.php文件
 - 可自行设置 重定向 优化

*/
