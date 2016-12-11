<?php
/*
 * 首页模板和通用配置
/*/
$_va_home = array(

	//config配置
	'c' => array(
		'vmode' => 'dynamic', //dynamic,static,close(关闭)
		'stext' => '.html', //后缀: .html, .htm, .shtm, -a.htm, -m.htm, .shtml, .stm,
		'stexp' => '2h', //静态更新周期：600s,30,2h,24h,7d, 默认单位为分钟
	),
	
	//mod.home模块首页模板
	'm' => 'c_page/_home',
	
	//关闭模块
	'close' => array('indoc','about'),
	//文档/资讯:默认按va_docs设置
	//其他未设置模块按关闭处理
	
	//import导入配置的模块(import不支持静态)
	'imcfg' => array(
        //'gbook' => 'nrem', // gbook按nrem方式显示
		'crem' => 'nrem', 
		'trem' => 'nrem',
		'kerem' => 'nrem',
		'drem' => 'nrem',
		//文档/资讯 => 默认按va_docs.php配置
	),
	
	//扩展模块
	'extra' => array(
		'home','info','tools',
		'start','tester','tpltag','dev2nd','advset','uplog' //'coder',
	), 

);
