<?php 
require(dirname(__FILE__).'/_config.php');  

glbHtml::page("Create Suit Template - (sys_name)",1);
glbHtml::page('imin');
#echo basJscss::imp("/tools/exdiy/sfunc.js");
echo basJscss::imp("/tools/exdiy/style.css");
glbHtml::page('body');

$dir = basReq::val('dir');
$front = basReq::val('front');
$mod = basReq::val('mod');
$dmsg = '';

if($dir && $front && $mod){
	$flag = 'done';
	$dmsg = devApp::create($dir, $front, $mod);
	$smsg = $dmsg=='OK' ? '创建成功' : '创建失败:'.$dmsg;
	$fmsg = $dmsg=='OK' ? 'OK' : '错误';
}

require(dirname(__FILE__).'/mktpl.htm');

glbHtml::page('end');

