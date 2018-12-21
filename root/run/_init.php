<?php
namespace imcat;
// (自v3.8开始) `设置站点相对目录` 等路径请移步至 `/root/cfgs/boot/_paths.php`

// 页面_cbase, 运行时常用变量, 基本常量 
$_pbase = empty($_cbase) ? array() : $_cbase; // 页面_cbase: run.outer, skip.*, tpl.vdir, 
$_cbase['run']['timer']  = microtime(1); // 越是最先运行越准确 
$_cbase['run']['memory'] = memory_get_usage();
$_cbase['run']['aclass'] = array(); 
define('DIR_PROJ', dirname(dirname(__DIR__))); // 项目根目录 
defined('RUN_INIT') || define('RUN_INIT', 1); // 初始化标记
defined('DS') || define('DS', DIRECTORY_SEPARATOR); // 目录分隔符

// 加载:系统路径配置,常规配置,自动加载类
include DIR_PROJ.'/root/cfgs/boot/_paths.php'; // 加载系统路径配置
require DIR_ROOT.'/cfgs/boot/const.php'; // 基本设置const: 可被后台设置,页面设置覆盖
require DIR_DTMP.'/dset/_score.cfg.php'; // 后台设置_score: 其次,可被页面设置_pbase覆盖
#require DIR_ROOT.'/cfgs/boot/_score.min.php'; // mini模式用此文件
require DIR_IMCAT.'/core/blib/loader.php'; // 包含自动加载类(仅包含文件)
#require DIR_VENDOR.'/autoload.php'; // composer 的自动加载

basLoader::init(); // 初始化-类自动加载
// 处理outer运行模式
if(empty($_cbase['run']['outer'])){ 
    require DIR_IMCAT.'/core/blib/helper.php'; // 别名函数
    basEnv::runPbase($_pbase); unset($_pbase); //处理_pbase 
    basEnv::runVersion(); // 系统信息,魔术变量,时区
    basEnv::runConst(); // (*)const,
    basEnv::runCbase(); // (*)前置处理,运行时常用变量
    basEnv::runSkips(); // 处理skips
    safComm::urlQstr7(); // QUERY-7参数检测 
    if(!empty($_cbase['ucfg'])){
        basLang::auto(); // 自动语言/皮肤配置
    }
} 
