<?php
// 初始化标记,项目路径,同时用于全站判断是否加载了初始化文件; 
define('RUN_INIT', dirname(dirname(dirname(__FILE__)))); 

//加载系统路径配置
include(RUN_INIT.'/code/cfgs/boot/_paths.php');

//加载启动文件
include(RUN_INIT.'/code/cfgs/boot/bootstart.php');

