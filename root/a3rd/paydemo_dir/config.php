<?php
require(dirname(dirname(__FILE__)).'/a3rd_cfgs.php'); 
define('DIR_PAYRUN', dirname(__FILE__)); 
define('PATH_PAYRUN', PATH_ROOT.'/a3rd/paydemo_dir');
define('LIBS_PAYRUN', DIR_PAYRUN);

function dmgetPost(){
	$post = array();
	foreach($_POST as $k=>$v){
		if(!strstr($k,'_post_')) continue;
		$key = str_replace('_post_','',$k);
		$post[$key]	= $v;
	}
	return $post;
}
function dmdoSend(){
	global $_cbase;
	$data = dmgetPost();
	$dobj = array();
	$cfgorg = array('payment_type','out_trade_no','subject','total_fee','body');
	$cfgconv = array('service'=>'exterface','partner'=>'seller_id','seller_email'=>'seller_email');
	$cfgadd = array('is_success'=>'T','sign_type'=>'TRADE_SUCCESS','sign_type'=>'MD5','notify_type'=>'trade_status');
	foreach($cfgorg as $key){
		$dobj[$key] = $data[$key];
	}
	foreach($cfgconv as $key=>$val){
		$dobj[$val] = $data[$key];
	}
	foreach($cfgadd as $key=>$val){
		$dobj[$key] = $val;
	}
	$dobj['buyer_email'] = $dobj['buyer_id'] = basReq::ark('fm','uname');
	$dobj['trade_no'] =  basKeyid::kidTemp('(def)').'-'.basKeyid::kidRand('24',8);
	$dobj['notify_time'] = $_cbase['run']['stamp'];
	$dobj['notify_id'] = "{$dobj['notify_time']}.{$dobj['trade_no']}";
	$dobj['sign'] = comConvert::sysEncode($dobj['notify_id']);
	//通过curl post数据 ($data: str:xml,str:json,array)
	$sobj = '';
	foreach($dobj as $key=>$val){
		$sobj .= ($sobj ? '&' : '')."$key=$val";	
	}
	$urls = array('notify','return');
	foreach($urls as $key){
		$url = $data["{$key}_url"];
		$$key = $url.(strpos($url,'?') ? '&' : '?').$sobj;
	}
	$notice = comHttp::doGet($notify, $data, 3);
	//echo $return;
	header('Location:'.$return);
}
function dmdoCheck(){
	global $_cbase;
	$stamp = $_cbase['run']['stamp'];
	$notify_time = basReq::val('notify_time');
	$trade_no = basReq::val('trade_no');
	$sign = basReq::val('sign');
	$enc = comConvert::sysEncode("$notify_time.$trade_no");
	$flag = ($enc==$sign) && ($stamp-$notify_time<60); //var_dump($flag);
	return $flag;
}

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者id，以2088开头的16位纯数字
$demopay_config['partner']		= $pay_uinfo['demo']['partner'];

//收款演示账号
$demopay_config['seller_email']	= $pay_uinfo['demo']['email'];

//安全检验码，以数字和字母组成的32位字符
$demopay_config['key']			= $pay_uinfo['demo']['key'];

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

//签名方式 不需修改
$demopay_config['sign_type']    = strtoupper('MD5');

//字符编码格式 目前支持 gbk 或 utf-8
$demopay_config['input_charset']= strtolower('utf-8');

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$demopay_config['transport']    = 'http';
?>