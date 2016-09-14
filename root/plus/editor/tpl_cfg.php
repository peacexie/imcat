<?php
require('_config.php');
glbHtml::head('html');
$fid = basReq::val('fid','content');
$pSub = basReq::val('pSub','peace'); // peace,baidu,eweb //// peace,def
$lang = $_cbase['sys']['lang']; 

glbHtml::page(lang('plus.edt_tplchar'),1);
glbHtml::page('imadm'); //adm
basLang::jimp("/plus/editor/_pub.js",'',$lang,0);
#echo basJscss::imp("/plus/editor/_pub.js");
#echo basJscss::imp("/plus/editor/_pub-$lang.js");
echo basJscss::imp("/plus/editor/tpl_style.css");
glbHtml::page('body');

$itpl = lang('plus.edt_tpl');
$ichr = lang('plus.edt_spchar');

echo  "\n<table class='file_bar'><tr>
<td ".($pSub=='peace'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=peace'>$ichr(peace)</a></td>
<td ".($pSub=='baidu'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=baidu'>$ichr(baidu)</a></td>
<td ".($pSub=='eweb'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=eweb'>$ichr(eweb)</a></td>
<td ".($pSub=='align'?'class="act"':'')."><a href='tpl_doc.php?fid=$fid&pSub=align'>$itpl(align)</a></td>
<td ".($pSub=='common'?'class="act"':'')."><a href='tpl_doc.php?fid=$fid&pSub=common'>$itpl(common)</a></td>
</tr></table>";
?>
