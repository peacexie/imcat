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
	$smsg = $dmsg=='OK' ? lang('tools.mktpl_crtok') : lang('tools.mktpl_crtng').$dmsg;
	$fmsg = $dmsg=='OK' ? 'OK' : lang('tools.mktpl_error');
}

include(vopShow::inc('/tools/exdiy/mktpl.htm',DIR_ROOT));

glbHtml::page('end');
