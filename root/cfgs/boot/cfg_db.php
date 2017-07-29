<?php

// 数据库 - 基本配置

$_cfgs['db_class']   = 'mysqli'; // 数据库类(class),mysqli(推荐),pdox(用于PDO扩展),mysql(PHP5.5+不能使用)
  if($_SERVER["HTTP_HOST"]=='txmao.txjia.com'){
$_cfgs['db_host']    = 'sqld.duapp.com'; // 数据库主机(pdox连接不使用)
$_cfgs['db_name']    = 'MoyBZpUtOrBXQRVXQjyn'; // 数据库名(pdox连接不使用) 
$_cfgs['db_port']    = '4050'; // 数据库端口，mysql默认是3306，一般不需要修改
$_cfgs['db_user']    = '2c9e5dc8524f46558b0bfa1f8a592b98'; // 数据库用户名
$_cfgs['db_pass']    = '48b00a2a78184fe08c830e71af7e026a'; // 数据库密码 
  }else{
$_cfgs['db_host']    = 'localhost'; // 127.0.0.1,localhost,数据库主机(pdox连接不使用)
$_cfgs['db_name']    = 'txmao_main'; // 数据库名(pdox连接不使用) 
$_cfgs['db_port']    = '3306'; // 数据库端口，mysql默认是3306，一般不需要修改
$_cfgs['db_user']    = 'imcat'; // 数据库用户名
$_cfgs['db_pass']    = '123456'; // 数据库密码
  }

// 数据库 - 高级配置
#$_cfgs['db_dsn']  = 'mysql:host=localhost;dbname=peace_v30'; //pdox连接access使用
#$_cfgs['db_dsn']   = 'sqlite:'.DIR_URES."/@dbfs/txmao_main.imcat!db3"; 
$_cfgs['db_type']   = 'mysql'; // 数据库类型,mysql/sqlite; 目前只支持mysql
$_cfgs['db_conn']   = false; // true表示使用永久连接，false表示不适用永久连接，一般不使用永久连接
$_cfgs['db_prefix'] = ''; // 数据库前缀pre_(为了与其他系统最大兼容性,前缀/后缀一般只用一个,另一个留空)
$_cfgs['db_suffix'] = '_ys'; // 数据库后缀_suf
$_cfgs['db_cset']   = 'utf8';// 数据库编码

// 数据库缓存
$_cfgs['dc_on']      = 1; //
//$_cfgs['dc_type']    = ''; // 不设置为空默认
$_cfgs['dc_prefix']  = 'cdb_';
$_cfgs['dc_path']    = '/cacdb'; // /cacdb
$_cfgs['dc_exp']     = 60; // 600~3600(s)