<?php 
$_cbase['ucfg']['lang'] = '(auto)'; 
require(dirname(__FILE__).'/root/run/_init.php'); 

$qstr = $_SERVER['QUERY_STRING'];
$proot = devRun::prootGet();

if(strpos($qstr,'_close')){ //关闭的跳转 //mob_close
	if(!empty($_cbase['close_'.str_replace('_close','',$qstr)])){
		require(DIR_CODE.'/cfgs/stinc/close_info.php');
	}
}elseif($qstr=='start' || $proot!=PATH_PROJ){ //起始页
	$qstr = $proot!=PATH_PROJ ? "?FixProot" : '';
	header("Location:./root/tools/adbug/start.php{$qstr}"); 
}elseif(!empty($_cbase['close_chn'])){ //电脑版是否关闭
	include(DIR_CODE."/cfgs/stinc/close_info.php");
}elseif(!empty($qstr)){	//处理跳转
	require(DIR_ROOT.'/plus/ajax/redir.php');
	header('Location:?');
}else{ //默认页
	//header('Location:chn.php'); //直接跳转到首页
	//require(DIR_ROOT.'/tools/rhome/home.php'); //原生代码
	vopShow::inc('/tools/rhome/home.htm',DIR_ROOT,1); //通过模板解析
	#include(vopShow::inc('/tools/rhome/home.htm',DIR_ROOT));
} 
 