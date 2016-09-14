<?php
/*
 * topic模板配置
/*/
$_vc_topic = array(

	//config配置
	'c' => array(
		'vmode' => 'dynamic', //dynamic,static,close
		'stext' => '.html',
		'stexp' => '2h', //hour(s)
	),
	
	//mod.home模块首页
	'm' => array(
		'0' => 'c_mod/topic_list', //首页(key=0,first; val=list,home
		'list' => 'c_mod/topic_list', //搜索
	), 
	
	//详情页
	'd' => 'c_mod/topic_detail',
	
	//类别页
	't' => 'c_mod/topic_list',
	
	//单个类别(模板)
	#'serv' => 'c_mod/{mod}_serv', //服务内容

);
