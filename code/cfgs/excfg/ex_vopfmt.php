<?php
//模板显示格式

$_ex_vopfmt = array();

// 存储格式
$_ex_vopfmt['res'] = array( 
	'0' => array('about'), // yyyy-md-noid
	'1' => array('news'), // yyyy/md-noid默认
	'2' => array('cargo'), // yyyy-md/noid
	'3' => array('demo'), // yyyy/md/noid
);	

// 所有语言
$_ex_vopfmt['langs'] = array(
    'en' => array(
		'English',  
		'En'
    ),
	'cn' => array(
		'中文版',  
		'中'
	), 
);

// 所有模板
$_ex_vopfmt['tpl'] = array(
    'adm' => array(
    	array('cn'=>'管理中心', 'en'=>'Admin'),
    	'/root/run/adm.php'
    ),
	'chn' => array(
		'中文版',  
		'/chn.php'
	), 
	'dev' => array(
		'演示版',  
		'/dev.php'
	), 
	'doc' => array(
		'Guides',  
		'/doc.php'
	), 
	'mob' => array(
		array('cn'=>'手机版', 'en'=>'Mobile'),  
		'/mob.php'
	), 
	'umc' => array(
		array('cn'=>'会员中心', 'en'=>'User'),
		'/root/run/umc.php'
	), 
	//'demodir' => array('hello','/root/run/front.php'), 
);

// 各模块展示show
$_ex_vopfmt['show'] = array( 
	//'chn' => array('',''), 
	'dev' => array('demo'), 
	'umc' => array('indoc','faqs'), 
	'_defront_' => 'chn', //默认展示模板
	'_deadmin_' => 'adm', //默认管理模板
	'_hidden_' => array('adminer','inmem'), //无展示模块
);
