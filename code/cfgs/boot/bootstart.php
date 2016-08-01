<?php
(!defined('RUN_MODE')) && die('No Init');

$_pbase = empty($_cbase) ? array() : $_cbase; //把前置_cbase备份
//$_cbase = array(); run.outer, skip.*, tpl.tpl_dir, 

// 运行时常用变量,越是最先运行越准确
$_cbase['run']['timer']  = microtime(1); 
$_cbase['run']['memory'] = memory_get_usage(); 
$_cbase['run']['aclass'] = array(); //

// 加载系统函数,配置
require(DIR_CODE.'/core/blib/system.php'); 
require(DIR_CODE.'/cfgs/boot/const.cfg.php'); //基本设置const: 可被后台设置,页面设置覆盖
require(DIR_DTMP.'/dset/_score.cfg.php'); //后台设置_score: 其次,可被页面设置覆盖
//以下会处理[页面设置_pbase];         //页面设置_pbase: 最优先

// 加载启动文件
if(empty($_cbase['run']['outer'])){
	// 加载runskip
	if(isset($_cbase['skip'])){
		include(DIR_CODE.'/cfgs/boot/bootskip.php'); 
	}
	// 加载autoload
	autoLoad_ys::init();
	// 全局系统配置
	if(!empty($_pbase)){ 
		if(!empty($_pbase)) $_cbase = basArray::Merge($_cbase, $_pbase);
		unset($_pbase);
	}
	// 系统信息,魔术变量,时区
	basEnv::runVersion();
	// const,
	basEnv::runConst();
	// 前置处理,运行时常用变量
	basEnv::runCbase();
	// QUERY-7参数检测
	safComm::urlQstr7(); 
	// 错误处理类 
	# empty($phpviewerror) || @ini_set('display_errors', 'On');
	if(!isset($_cbase['skip']['error'])){
		basEnv::runError();
	}
	// *** robot
	if(isset($_cbase['skip']['robot'])){
		safBase::robotStop(); 
	}
	// 处理session
	if(!isset($_cbase['skip']['session'])){ 
		if(!session_id()) @session_start();
	}
	// 扩展
}

/// 其它处理参考 ///////////////////////////////////

// forTest
#echo "runInfo:"; print_r(basDebug::runInfo()); 

