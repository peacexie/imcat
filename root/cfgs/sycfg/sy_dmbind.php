<?php
$_sy_dmbind = array(

	// /vary/html/cargo/2015-9c/h3h1.html
	// http://pro.my_domain.com/2015-9c/h3h1.html
	/*
	array(
		'{html}/xxx_cargo/`d#`', //用`d#`,`w#` 分别代表正则的 (\d+)(\w+)
		'http://pro.my_domain.com/\\1-', //使用\1的形式来实现反向引用
		'1', //1-用正则-自动格式化
	),*/
	
);

/*

// /vary/html/cargo/2015-9c/h3h1.html
// /pro1/2015-9c/h3h1.html
array(
	'{html}/cargo/', //{html}=PATH_HTML：/run/_init.php中定义，html文档的相对目录
	'/pro1/',
	'0', //0-不用正则
),

// /vary/html/cargo/2015-9c/h3h1.html
// http://pro2.my_domain.com/2015-9c/h3h1.html
array(
	'{html}/cargo/`d#`', //用`d#`,`w#` 分别代表正则的 (\d+)(\w+)
	'http://pro2.my_domain.com/\\1-', //使用\1的形式来实现反向引用
	'1', //1-用正则-自动格式化
),

// 效果同上/08tools/yssina/vary/html/
array(
	'{html}\\/cargo\\/(\d+)\\-', 
	'http://pro3.my_domain.com/\\1-', 
	'2', //2-用正则-自由写正则
),

// 规则：正则表达式特殊字符有： . \ + * ? [ ^ ] $ ( ) { } = ! < > | : - 

*/
		