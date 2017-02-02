<?php
(!defined('RUN_INIT')) && die('No Init');

$rdo = 'fail';

/*
http://www.thinkphp.cn/extend/876.html
自动注册Hook机制钩子
*/

// code1: ex-dosth-1
// code2: ex-dosth-2
// code3: ...
basDebug::bugLogs('user_dosth','do-sth-N','detmp','db');

$rdo = 'pass';
