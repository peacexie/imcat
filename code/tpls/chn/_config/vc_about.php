<?php
/*
*/
$_vc_about = array(

	'c' => array(
		'vmode' => 'dynamic', //dynamic,close,static
		'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
        'stext' => '.html',
		'stypes' => array('afqas','anews','aserv'), //内容页生产静态的栏目,为空则不限制
	),
	'm' => 'first',
	'd' => 'm_about/about_detail',
	't' => 'm_about/about_page', //about,alink
	
	'anews' => 'm_about/about_list', //公司新闻
	'apics' => 'm_about/about_pics', //公司图片
	'aserv' => 'm_about/about_serv', //服务内容
	'afqas' => 'm_about/about_fqas', //常见问题
	'alink' => 'm_about/about_link', //联系我们
	
	'istest' => 'close',
	
	'v' => 'profile,awhua,alink', //可带view参数
	
	/*'d' => array(
		'0' => 'm_about/about_detail',
		'list1' => 'm_about/about_dlist1',
		'list2' => 'm_about/about_dlist2',
	)*/
	//'iutest' => 'aa/bb',
	
);
