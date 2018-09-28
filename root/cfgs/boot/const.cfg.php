<?php
// 常用参数
//$_cbase['run']['isDemo'] = 0; //演示站，不能提交数据
$_cbase['run']['defPort'] = 1; //默认端口，不带端口

/// 参数配置-根据需要配置 //////////////////////////////////////////////////////////////////////////////// 

// 系统参数
$_cbase['sys']['sn']      = '0BAB703D-127A-B479-1979-2010-0424X888'; //序列号
$_cbase['sys']['ver']     = '4.4'; // 版本号
$_cbase['sys']['cset']    = 'utf-8';// 系统编码
$_cbase['sys']['tmzone']  = '8'; //
$_cbase['sys']['tzcode']  = 'PRC'; // 时区+-12, 'ETC/GMT-8'
$_cbase['sys']['lang']    = 'cn'; // 默认语言:根据语言包,可设置en,cn等

// Cookie
$_cbase['ck']['pre']      = '044_'; // Cookie前缀,8字符以内
$_cbase['ck']['domain']   = ''; // Cookie Domain
$_cbase['ck']['path']     = '/'; // Cookie Path

//调试日志配置
$_cbase['debug']['err_mode']  = '1'; //可后台设置,是否开启调试模式，1,0(run=0)
$_cbase['debug']['err_save']  = 'file'; //记录方式: file, db, 
$_cbase['debug']['err_path']  = ''; //出错信息存放的目录，出错信息以天为单位存放，一般不需要修改 /erlog  
$_cbase['debug']['err_file']  = 'Y-md'; //文件格式: Y-md:1979-0913, Y-m-d: 1979-09-13
$_cbase['debug']['err_hand']  = true; //是否启动CP内置的错误处理，如果开启了xdebug，建议设置为false 
$_cbase['debug']['err_hkey']  = '(def)'; //E_ALL^E_WARNING^E_NOTICE;

$_cbase['debug']['log_save']  = ''; //记录方式: 空:不记录, Y-mdH, Y-md-H, db, 
$_cbase['debug']['pay_log'] = 1; //支付记录-调试标记

$_cbase['debug']['db_sql']  = ''; //记录方式: 空:不记录, Y-mdH, Y-md-H, db, 
$_cbase['debug']['db_acts'] = ',select,qSelect,find,'; //,delete,update,replace,insert,count,fields,tables,qOther,
$_cbase['debug']['db_area'] = array('RUN_FRONT','RUN_MOB',); //,'RUN_ADMIN','RUN_UMC','RUN_AJAX','RUN_DBSQL','RUN_DEV'
$_cbase['debug']['db_time'] = '100'; //0,10,100

//模板配置 
$_cbase['tpl']['tpl_ext'] = '.htm'; //模板后缀，建议为：[.htm]
$_cbase['tpl']['tpc_on']  = 0; //是否开启模板缓存，true开启,false不开启 
$_cbase['tpl']['tpc_ext'] = '.cac_php'; //模板缓存后缀,一般不需要修改 
$_cbase['tpl']['no_static'] = '(adm)'; //,umc
$_cbase['tpl']['def_static'] = 'chn';
$_cbase['tpl']['mob_tpls'] = '(mob)'; //,app

// server
$_cbase['server']['txmao']  = 'http://imcat.txjia.com'; //txmao首页{svrtxmao}
$_cbase['server']['txcode'] = 'http://yscode.txjia.com'; //txcode首页{svrtxcode}
$_cbase['server']['txjia']  = 'http://txjia.com'; //txjia首页{svrtxjia}

//用户自定义配置
$_cbase['ucfg']['vimg']  = 'K'; // 0,H,K
$_cbase['ucfg']['ipapi'] = 'pcoln'; // 默认IP地址接口, sina,taobao,pcoln,s1616
#$_cbase['ucfg']['czip']  = '0'; // zlib,gzip,0(不用了)
$_cbase['ucfg']['guid'] = 'Cook'; // UIP,Sess,Cook
$_cbase['ucfg']['city'] = '东莞'; //本地城市,订单算运费用
$_cbase['ucfg']['space'] = 30; //M空间大小
$_cbase['ucfg']['dbind'] = 0; //是否开启绑定子域名
$_cbase['ucfg']['ctab'] = 'F00,F0F,060,00F,F60,90F,F69,06F,099,606,60F,906,F6F'; //颜色表

/// 额外配置-根据需要配置 //////////////////////////////////////////////////////////////////////////////// 

// xdebug 

// weixin
$_cbase['weixin']['debug']  = true; 
$_cbase['weixin']['actiks']  = array(); 
$_cbase['weixin']['haibaoMediaid'] = 'I7mPnzk9v0tF6nHiEmy0sDbtRksBp4pVHSYBZvdROjc';
$_cbase['weixin']['haibaoPicurl'] = 'http://yscode.txjia.com/uimgs/logo/haibao.jpg';
$_cbase['weixin']['tplidIndoc'] = 'u6DK6CKG8TnCFGaOwglBPUPa_UvE3nwpQU-k8kP1YpA';

// indoc
$_cbase['indoc']['debug'] = true; 
// topic
$_cbase['topic']['tpldir'] = '/chn/topic'; 

// 3aks
$_cbase['3aks']['baiduip'] = '3GGtGlCtbAGa1GYK70XFX2Rb'; //百度IP
$_cbase['3aks']['googlemap'] = 'AIzaSyCz-pQkTS-XnB2l3kc9JeT-NICKxO8dc-g'; //google地图
$_cbase['3aks']['baidumap'] = ''; //百度地图

