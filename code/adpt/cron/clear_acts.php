<?php
(!defined('RUN_INIT')) && die('No Init');

// 1. 可用:db,stamp
// 2. 返回:$rdo = pass/fail

$rdo = 'fail';

$stnow = $stamp; // 432000=5day, 86400=1天 active_online

$db->table('active_admin')->where("stime<'".($stnow-432000)."'")->delete(); 
$db->table('active_online')->where("stime<'".($stnow-432000)."'")->delete(); 	
$db->table('active_session')->where("exp<'".($stnow-3600)."'")->delete();

$rdo = 'pass';

