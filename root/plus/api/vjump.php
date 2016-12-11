<?php
$_cbase['run']['subDirs'] = '1';
require(dirname(__FILE__).'/_config.php'); 

// ?debug,ip,cfgs,html
$qstr = $_SERVER['QUERY_STRING'];
// 获取ip,可在地址栏输入?ip用于调试
$userip = ($qstr && strpos($qstr,'.')) ? $qstr : comSession::getUIP(); 

#dump($qstr.$userip);
if($qstr=='nav'){
	$rip = '121.'.mt_rand(3,17).'.197.187';
	$data = array('nav','cfgs','html','lang','debug',$rip,$rip.':debug');
	exvFunc::navShow($data,1);
}elseif(in_array($qstr,array('cfgs','html'))){
	$data = exvJump::getCfgs('sites');
	$_def = exvJump::getCfgs('_defs');
	if($qstr=='html'){
		exvFunc::navShow($data,'{key}',"http://$key.{$_def['domain']}/");
	}else{
		echo comParse::jsonEncode($data);
	}
}elseif(in_array($qstr,array('lang'))){
	echo exvJump::getLang();
}else{
	if($qstr && strpos($qstr,':debug')){
		$qstr = 'debug';
		$userip = str_replace(':debug','',$userip);
	}
	// 获取:ip对应地址/跳转url
	$addr = exvJump::getAddr($userip);
	$durl = exvJump::getDurl($addr);
	if($qstr=='debug'){
		$data = array('userip'=>$userip,'addr'=>$addr,'dir_url'=>$durl,);
		exvFunc::navShow($data,0);
	}else{
		header("Location:$durl");  
	}
}

