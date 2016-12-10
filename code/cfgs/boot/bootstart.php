<?php
(!defined('RUN_INIT')) && die('No Init');

$_pbase = empty($_cbase) ? array() : $_cbase; //把前置_cbase备份
//$_cbase = array(); run.outer, skip.*, tpl.tpl_dir, 

// 运行时常用变量,越是最先运行越准确
$_cbase['run']['timer']  = microtime(1); 
$_cbase['run']['memory'] = memory_get_usage();
$_cbase['run']['aclass'] = array(); //

// 加载:自动加载类,别名函数,配置
require(DIR_CODE.'/core/blib/loader.php'); //自动加载类,别名函数
require(DIR_CODE.'/cfgs/boot/const.cfg.php'); //基本设置const: 可被后台设置,页面设置覆盖
require(DIR_DTMP.'/dset/_score.cfg.php'); //后台设置_score: 其次,可被页面设置覆盖
//以下会处理[页面设置_pbase];         //页面设置_pbase: 最优先

// 加载启动文件
if(empty($_cbase['run']['outer'])){ 
	// 加载autoload
	autoLoad_ys::init();
	// run常规处理
	basEnv::runPbase($_pbase); unset($_pbase); //处理_pbase 
	basEnv::runVersion(); // 系统信息,魔术变量,时区
	basEnv::runConst(); // const,
	basEnv::runCbase(); // 前置处理,运行时常用变量
	basEnv::runSkips(); // 处理skips
	safComm::urlQstr7(); // QUERY-7参数检测 
	basLang::auto(); // Lang
	// run扩展
}

/// 其它处理参考 ///////////////////////////////////

// forTest
#echo "runInfo:"; print_r(basDebug::runInfo()); 

