<?php
//类的自动加载规范(路径)

//* 核心类库(前缀及目录对照表)
$_cfgs['acdir'] =  array(
	'core/blib' => 'bas',//基本类库
	'core/clib' => 'com',//常规类库
	'core/elib' => 'ext',//扩展类库,安全检测
	'core/glib' => 'adm,fld,glb,saf',//全局专项类库:管理函数,字段处理,全局公用,安全检测
	'core/vops' => 'tag,vop',//标签类库,显示类库
	'core/dops' => 'dop,usr',//数据操作类库,用户权限类库
	'core/sdev' => 'dev,exd,upd',//二次开发,数据扩展,更新相关
	'core/uext' => 'exa,exm,exv',//后台扩展,会员扩展,前台扩展
	'adpt/wechat' => 'wmp,wys',//微信类库
	'adpt/weuser' => 'wex',//微信扩展
	#'libu/libx1' => 'ex1',//自定义路径和前缀
	#'libu/dirx2' => 'ex2,dir',//自定义路径和前缀
	#'libu/catu3' => 'eu3,cat',//自定义路径和前缀
);//*/

// 符合上述配置的类文件（不使用命名空间(php5.2无)），放在code/下相关目录
// 上述配置未找到类文件的，会按以下第三方目录的规范去找类文件

//2. 第三方-pr4规范
$_cfgs['acpr4'] =  array(
	'Vdemo' => array('/Vdemo'), //vendor加载演示
	//'Monolog' => array('/Monolog'), 
	//'Symfony' => array('/Symfony'),
);

//3. 第三方-namespace规范
$_cfgs['acnsp'] =  array(
	//'Buzz' => array('/ksbuzz/lib'),
    //'Psr\\Log' => array('/psrlog'),
    //'Silex' => array('/silex/silex/src'),
);

//4. 自定义-classmap规范
$_cfgs['acmap'] =  array(
    //'basArray' => '~/core/blib/basArray.php',
    //'mTest1' => '/Vdemo/dir1/mapTest1.php',
    //'Pimple' => '/pimple/lib/Pimple.php',
    //'dir\\Name' => '/dir/Name.php',
);

/*

*/

