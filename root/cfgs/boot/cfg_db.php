<?php

// 数据库 - 基本配置

$_cfgs['db_class']   = 'mysqli'; // 数据库类(class),mysqli(推荐),pdox(用于PDO扩展),mysql(PHP5.5+不能使用)
$_cfgs['db_host']    = 'localhost'; // 数据库主机(pdox连接不使用)
$_cfgs['db_name']    = 'catv37_20176b'; // 数据库名(pdox连接不使用) 
$_cfgs['db_port']    = '3306'; // 数据库端口，mysql默认是3306，一般不需要修改
$_cfgs['db_user']    = 'root'; // 数据库用户名
$_cfgs['db_pass']    = ''; // 数据库密码    

// 数据库 - 高级配置
#$_cfgs['db_dsn']  = 'mysql:host=localhost;dbname=peace_v30'; //pdox连接access使用
#$_cfgs['db_dsn']  = 'sqlite:'.DIR_URES."/@dbfs/txmao_main.imcat!db3"; 
$_cfgs['db_type']   = 'mysql'; // 数据库类型,mysql/sqlite; 目前只支持mysql
$_cfgs['db_conn']   = false; // true表示使用永久连接，false表示不适用永久连接，一般不使用永久连接
$_cfgs['db_prefix'] = ''; // 数据库前缀pre_
$_cfgs['db_suffix'] = '_ys'; // 数据库前缀_suf
$_cfgs['db_cset']   = 'utf8';// 数据库编码

// 数据库缓存
$_cfgs['dc_on']      = 0; //
$_cfgs['dc_type']    = ''; // cacheMemd,cacheMemc,cacheSaem
$_cfgs['dc_prefix']  = 'dFx'; 
$_cfgs['dc_server']  = '127.0.0.1';
$_cfgs['dc_port']    = '11211';
$_cfgs['dc_pconn']   = '1';
$_cfgs['dc_tmout']   = '600';
$_cfgs['dc_size']    = '15'; //M

// 多库配置 - 使用:$db=db('user');
// 处理多库: active, users, wex, 
// 不处理多库: advs base bext coms dext docs exd init logs plus types  
/* 
$_cfgs['user'] = $_cfgs;
$_cfgs['user']['db_host']    = 'localhost'; // 127.0.0.1,localhost,数据库主机(pdox连接不使用)
$_cfgs['user']['db_name']    = 'txmao_main_user'; // 数据库名(pdox连接不使用) 
//*/
