<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

// 1. 可用:db,stamp
// 2. 返回:$rdo = pass/fail

$rdo = 'fail';

// code1: ex-dosth-1
// code2: ex-dosth-2
// code3: ...
basDebug::bugLogs('hook_demo','do-sth','detmp','db');

$rdo = 'pass';

/*
http://www.thinkphp.cn/extend/876.html
自动注册Hook机制钩子
*/
