<?php
(!defined('RUN_INIT')) && die('No Init');

// 1. ¿ÉÓÃ:db,stamp
// 2. ·µ»Ø:$rdo = pass/fail

$rdo = 'fail';

basDebug::bugLogs('hook_demo','do-sth','detmp','db');

$rdo = 'pass';
