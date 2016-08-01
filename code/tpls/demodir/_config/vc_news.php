<?php
/*
 * 文档通用模板配置
/*/
$_vc_news = array(

	//config配置
	'c' => array(
		'vmode' => 'dynamic', //dynamic,static,close
		'stext' => '-t.htm',
		'stexp' => '2h', //hour(s)
	),
	
	//mod.home模块首页
	'm' => array(
		'0' => 'c_mod/news_list', //首页
		'list' => 'c_mod/news_list', //搜索
	), 
	
	//详情页
	'd' => 'c_mod/news_detail',
	
	//类别页
	't' => 'c_mod/news_list',
	
	//单个类别(模板)
	#'serv' => 'c_mod/{mod}_serv', //服务内容

);
