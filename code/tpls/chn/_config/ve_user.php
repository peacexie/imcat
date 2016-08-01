<?php
/*
 * user扩展信息模板配置
/*/
$_ve_user = array(

	'c' => array(
		'vmode' => 'dynamic', //dynamic,close,static
		'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
	),
	'm' => 'm_user/user_login',
	//'d' => 'c_mod/{mod}_detail',
	//'t' => 'c_mod/{mod}_one', //about,alink
	
	'login' => 'm_user/user_login',
	'apply' => 'm_user/user_apply',
	//'appdo' => 'c_page/user_acts',
	'getpw' => 'm_user/user_getpw',
	//'getdo' => 'c_page/user_acts',
	
);
