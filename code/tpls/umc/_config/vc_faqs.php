<?php
/*
 * faqs模板配置
/*/
$_vc_faqs = array(

	'c' => array(
		'vmode' => 'dynamic', //dynamic,close,static
		'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
		'stext' => '.htm',
	),
	
	'm' => array(
		'0' => 'faqs/list', //首页
		'list' => 'faqs/list', //搜索
	),
	'd' => 'faqs/detail',
	't' => 'faqs/list', 
	
	// 类别/栏目:就不要设置如下ID了，否则冲突
	'new' => 'faqs/list', //最新
	'tip' => 'faqs/list', //精华
	'hot' => 'faqs/list', //热门
	'tag' => 'faqs/tags', //标签
	
	//'v' => 'my,dep,pub', //可带view参数 

);
