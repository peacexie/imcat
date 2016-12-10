<?php
(!defined('RUN_INIT')) && die('No Init');

// 1. 可用:db,stamp
// 2. 返回:$rdo = pass/fail

$rdo = 'fail';

$stnow = $stamp; // 432000=5day, 86400=1天 active_online

foreach(array('wex_locate','wex_msgget','wex_msgsend','wex_qrcode') as $tabid){
	$whrex = $tabid=='wex_qrcode' ? " AND sid>'123000'" : '';
	$db->table($tabid)->where("atime<'".($stnow-432000)."'")->delete();
}

$rdo = 'pass';

