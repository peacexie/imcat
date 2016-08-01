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

// 所有模板
$_ex_vopfmt['tpl'] = array(
    'adm' => array('管理中心','/root/run/adm.php'),
	'chn' => array('中文版'  ,'/chn.php'), //keres,corp,user,pic,xmlrss,xmlmap
	'dev' => array('演示版'  ,'/dev.php'), //tag,tjs,imp,demo,types,tips
	'mob' => array('手机版'  ,'/mob.php'), //about,news,cargo,order
	'umc' => array('会员中心','/root/run/umc.php'), //user,indoc,order
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
