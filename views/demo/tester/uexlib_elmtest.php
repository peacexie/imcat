<?php
(!defined('RUN_INIT')) && die('No Init');
$data = comFiles::get(__DIR__.'/uexlib_elmhtml.php');

/*
$data = '{block:abody}abody{/block:abody}';
$key = 'abody';
$k1 = "{block:$key}"; $k2 = "{/block:$key}";
$val = basElm::getVal($data,array($k1,$k2)); echo "\n\n<hr>abody:val:\n$val\n";
*/

$val = basElm::getVal($data,'title'); echo "\n\n<hr>title-val:\n$val\n";
$val = basElm::getPos($data,'title'); echo "\n\n<hr>title-pos:\n$val\n";

$val = basElm::getVal($data,'id="link"(*)id="test"','->'); echo "\n\n<hr>val:\n$val>>>\n";
$val = basElm::getPos($data,'id="link"(*)id="test"'); echo "\n\n<hr>pos:\n$val>>>\n";

$val = basElm::getVal($data,'<div class="content">(*)</div>'); echo "\n\n<hr>val2:\n$val>>>\n";
$val = basElm::getPos($data,'<div class="content">(*)id="link"'); echo "\n\n<hr>pos2:\n$val>>>\n";
$val = basElm::getPos($data,'<div class="content">(*)</div>'); echo "\n\n<hr>pos3:\n$val>>>\n";

$val = basElm::getPos($data,'id="xnon15"(*)id="xnon32"'); echo "\n\n<hr>pos4:\n$val>>>\n";

$arr = basElm::getArr($data,'<li class(*)</li>'); echo "\n\n<hr>getArr:\n"; print_r($arr); echo "\n";
$arr = basElm::getPreg($data,'<li class="cls1">(*)</li>'); echo "\n\n<hr>getPreg:\n"; print_r($arr); echo "\n"; 

$arr = basElm::getAttr($data,'target','key'); echo "\n\n<hr>getArr-a:\n"; print_r($arr); echo "\n"; 
$val = basElm::getAttr($data,'target','key',1); echo "\n\n<hr>getAttr-no:\n$val\n";
$arr = basElm::getAttr($data,'noattr','key'); echo "\n\n<hr>getArr-a:\n"; print_r($arr); echo "\n"; 

$val = basElm::getAttr($data,'witdh','key',0); echo "\n\n<hr>getAttr-witdh:\n$val\n";
$arr = basElm::getAttr($data,'href','url'); echo "\n\n<hr>getArr-urls:\n"; print_r($arr); echo "\n"; 

/*

匹配标记：
匹配模式：Html标签(单个内容), 两点定位(单个内容), Html标签数组(或单个), Html标签正则(或单个), 属性数组(或单个)
扩展操作：获取网址内容[url:fatch]，获取远程图片[save:image]


内容来源页面     
字段内容采集模印

清除Html
替换信息来源内容
替换信息=>结果内容

结果处理函数

exd_psyn : fskip

fskip 从字段配置中剔除的字段
fdefs 内置字段或配置字段

orgtag1    varchar(255) []     
orgtag2    varchar(255) []     
orgtag3    varchar(255) []     

dealfmts    varchar(255) []     note,blank,html,strtotime,
dealtabs    varchar(255)     替换来源内容=空
dealconv    varchar(24) []     a=b
dealfunc    varchar(48) []     结果处理函数 
defval    varchar(255) []     
defover    tinyint(4) [0]    

http://127.0.0.1/08tools/yssina/1/root/run/dev.php?tester-frame&code=uexlib_elmtest
view-source:http://127.0.0.1/08tools/yssina/1/root/run/dev.php?info-coder&tpls=tester/ubasic_baselm.php

modeVal(:)<title>(^)</title>  
  //  getVal($xStr,$flag) <title>采集测试(文章标题)</title>

modeArr(:)<td class="tc1">(*)</td>(^)3 
  // getArr($xStr,$flg1,$flg2) 从一组<td class="tc1">内容xxx</td>中，取第3个

modePos(:)id="rollImg01"(^)id="linknav"(^)+5(^)-10
modePos(:)id="rollImg01"(^)id="linknav"(^)+<td(^)-</div>

modeAttr(:)width(^)key  
  // getAttr($str,$flag,$reg='key') <td width="24">

(:)url:fatch
(:)save:image
  
*/

?>

<hr>



