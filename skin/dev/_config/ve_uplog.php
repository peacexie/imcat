<?php
(!defined('RUN_INIT')) && die('No Init');
/*
 * uplog扩展信息模板配置
/*/
$_ve_uplog = array(

	'c' => array(
		'vmode' => 'dynamic', //dynamic,close,static
		'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
	),
	'm' => 'c_demo/uplog_main',
	'd' => 'c_demo/uplog_main',
	//'t' => 'c_mod/{mod}_one', //about,alink
	
	/*
	...... (这里不用定义,有如下a_cfgs.php文件定义)
	'3_2' => 'c_demo/uplog_main', 
	'3_1' => 'c_demo/uplog_main',
	'3_0' => 'c_demo/uplog_main', 
	'2_x' => 'c_demo/uplog_main', 
	*/
	
);

include(dirname(dirname(__FILE__)).'/d_uplog/a_cfgs.php');
foreach ($cfgs as $key => $v) {
	if(preg_match("/\d{1}_\w{1}/",$key)){
		$_ve_uplog[$key] = 'c_demo/uplog_main';
	}
}
