<?php
namespace imcat;
$_cbase['tpl']['vdir'] = 'base';
require __DIR__.'/_config.php';        

glbHtml::page("Create Suit Template - (sys_name)",1);
eimp('initJs','jquery');
eimp('initCss','stpub;/tools/exdiy/style.css');
echo glbHtml::wpscale(480, 1);
glbHtml::page('body');

$part = req('part','tpl');
$dir = req('dir');
$front = req('front');
$mod = req('mod');
$org = req('org');
$obj = req('obj');
$dmsg = '';

if($dir && $front && $mod){
    $flag = 'done';
    $dmsg = devBuild::create($dir, $front, $mod);
    $smsg = $dmsg=='OK' ? lang('tools.mktpl_crtok') : lang('tools.mktpl_crtng').$dmsg;
    $fmsg = $dmsg=='OK' ? 'OK' : lang('tools.mktpl_error');
}elseif($org && $obj){
    $dmsg = devBuild::clang($org, $obj);
}

include __DIR__.'/build.htm';

glbHtml::page('end');
