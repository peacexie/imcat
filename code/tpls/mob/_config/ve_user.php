<?php
/*
 * user扩展信息模板配置
/*/
$_ve_user = array(

	'c' => array(
		'vmode' => 'dynamic', //dynamic,close,static
		'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
	),
	
	'm' => 'm_user/uhome',
	//'d' => 'c_mod/{mod}_detail',
	//'t' => 'c_mod/{mod}_one', //about,alink
	
	'wxlogin' => 'm_user/wxlogin',
	'wxlocal' => 'm_user/wxlocal',
	#'apply' => 'm_user/user_apply',
	#'getpw' => 'm_user/user_getpw',
	
);
