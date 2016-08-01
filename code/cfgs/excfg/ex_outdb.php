<?php
(!defined('RUN_MODE')) && die('No Init');

// 同步服务器 'http://master.domain.com/root';
$_ex_outdb['psyn']['server'] = 'http://127.0.0.1/txmao'; //同步服务器
$_ex_outdb['sign']['sapp'] = 'tiexinmao_demo_sapp'; //签名:sapp
$_ex_outdb['sign']['skey'] = 'X9040f@txmao-demo.skey'; //签名:skey
// 单独设置(暂未使用...)
#$_ex_outdb['sign_(jobid)']['sapp'] = ''; //签名:sapp
#$_ex_outdb['sign_(jobid)']['skey'] = ''; //签名:skey

// 外部数据库(键值不要用:psyn,sign,list等)
$_ex_outdb['list'] = array(
	'demodata' => '(demo)导入演示数据',
	'dedecms' => '(Mysql)织梦CMS', 
	'powereasy' => '(Access)动易CMS',
	'u08house' => '(u08house)08旧房产',
	'newhouse' => '(newhouse)08新房产',
);

$_ex_outdb['demodata']['db_type'] = 'mysql';
$_ex_outdb['demodata']['db_dsn'] = 'mysql:host=localhost;dbname=odata_demo;port:3306';
$_ex_outdb['demodata']['db_name'] = 'odata_demo';
$_ex_outdb['demodata']['db_user'] = 'root';
$_ex_outdb['demodata']['db_pass'] = '';
$_ex_outdb['demodata']['db_cset'] = 'utf8';

$_ex_outdb['dedecms']['db_type'] = 'mysql';
$_ex_outdb['dedecms']['db_dsn'] = 'mysql:host=localhost;dbname=dedecms_v57;port:3306';
$_ex_outdb['dedecms']['db_name'] = 'dedecms_v57';
$_ex_outdb['dedecms']['db_user'] = 'root';
$_ex_outdb['dedecms']['db_pass'] = '';
$_ex_outdb['dedecms']['db_cset'] = 'gbk';

$_ex_outdb['powereasy']['db_type'] = 'MSSQL'; //???
$_ex_outdb['powereasy']['db_dsn'] = "odbc:driver={microsoft access driver (*.mdb)};dbq=".DIR_DTMP."/upath/powereasy.mdb";
$_ex_outdb['powereasy']['db_user'] = '';
$_ex_outdb['powereasy']['db_pass'] = '';
$_ex_outdb['powereasy']['db_cset'] = 'gbk';


// 旧版数据，（用于[更新升级]#导入旧版数据）
$_cfgs['db_name']   = 'txmao_v31'; // 数据库名(pdox连接不使用) 
$_cfgs['db_prefix'] = ''; // 数据库前缀pre_
$_cfgs['db_suffix'] = '_ys'; // 数据库前缀_suf
$_cfgs['db_cset']   = 'utf8';// 数据库编码
// db_host,db_port,db_user,db_pass使用相同的设置
$_ex_outdb['psyn']['odbcfgs'] = $_cfgs;

/*
使用PDO链接外部数据库；在PHP.INI配置文件中找到php_pdo.dll，php_pdo_odbc.dll等配置设置
*/

$_ex_outdb['u08house']['db_type'] = 'mysql'; //???
$_ex_outdb['u08house']['db_dsn'] = "mysql:host=192.168.1.60;dbname=dbhouse;port:3306";
$_ex_outdb['u08house']['db_user'] = '';
$_ex_outdb['u08house']['db_pass'] = '';
$_ex_outdb['u08house']['db_cset'] = 'gbk';

$_ex_outdb['newhouse']['db_type'] = 'mysql'; //???
$_ex_outdb['newhouse']['db_dsn'] = "mysql:host=192.168.1.60;dbname=newhouse;port:3306";
$_ex_outdb['newhouse']['db_user'] = '';
$_ex_outdb['newhouse']['db_pass'] = '';
$_ex_outdb['newhouse']['db_cset'] = 'utf8';

