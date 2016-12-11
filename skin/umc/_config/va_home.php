<?php
/*
? 
*/
$_va_home = array(

	'c' => array(
		'vmode' => 'dynamic', //dynamic,catch,close
		'stexp' => '2h', //30,60,3h,6h,12h,24h,7d
		'stext' => '-u.htm', 
	),
	
	//mod.home模块首页模板
	'm' => 'user/home',
	
	//关闭模块
	'close' => array('xxxxx'),
	
	//import导入配置的模块
	'imcfg' => array(
		#'demo' => 'news', // demo按news方式显示
	),
	
	//扩展模块
	'extra' => array('user','order','help'), 
	//info : ,home, noperm, 
	//user : ,edit, edpw, 
	//order : ,list, detail, 
	
	// 权限配置：哪个模板用什么权限？
	// 键与模板关联，如：[tplname] => user/uinfo 则对应 [user][uinfo]；
	// 值与权限设置中的[会员]里面的标示对应
	// !isset-登陆, ''(空)-游客可用, 'xxx'-按设置
	//*
	'u' => array(
		'umc_frees' => array(-1,'faqs','help'), //umc-不需要登录模型
		//'login' => 'chn:user-login', //登录地址，用于提示
		//'apply' => 'chn:user-apply', //注册地址，用于提示
	),//*/
	
);
	