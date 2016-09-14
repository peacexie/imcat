<?php
(!defined('RUN_MODE')) && die('No Init');

// 1. ¿ÉÓÃ:db,stamp
// 2. ·µ»Ø:$re = array('rdo'=>'pass/fail')

$rdo = 'fail';

usleep(5);
// dosth

$rdo = 'pass';

/*

$db->table('active_online')->where("stime<'".($stnow-432000)."'")->delete(); 	
$db->table($this->tab)->data($data)->where("kid='$file'")->update(); 
$list = $db->table($tab)->field($cfg[1])->where($cfg[0])->select();
	foreach($list as $row){
		$fa = explode(',',$cfg[1]);
	}
}
			
*/

