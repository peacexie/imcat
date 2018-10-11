<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$rdo = 'fail';

$stnow = $stamp; // 432000=5day, 86400=1Ìì active_online

$db->table('active_admin')->where("stime<'".($stnow-432000)."'")->delete(); 
$db->table('active_online')->where("stime<'".($stnow-432000)."'")->delete(); 	
$db->table('active_session')->where("exp<'".($stnow-3600)."'")->delete();

$rdo = 'pass';
