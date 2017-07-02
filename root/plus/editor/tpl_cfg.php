<?php
require '_config.php';
glbHtml::head('html');
$fid = req('fid','content');
$pSub = req('pSub','peace'); // peace,baidu,eweb //// peace,def
$lang = $_cbase['sys']['lang']; 

glbHtml::page(lang('plus.edt_tplchar'),1);
imp('initJs','jquery,bootstrap,layer;comm;comm(-lang);/plus/editor/_pub;/plus/editor/_pub(-lang)');
imp('initCss','bootstrap,stpub,jstyle;comm;/plus/editor/tpl_style'); 
glbHtml::page('body');

$itpl = lang('plus.edt_tpl');
$ichr = lang('plus.edt_spchar');

echo  "\n<table style='margin:auto' class='table table-hover'><tr>
<td ".($pSub=='peace'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=peace'>$ichr(peace)</a></td>
<td ".($pSub=='baidu'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=baidu'>$ichr(baidu)</a></td>
<td ".($pSub=='eweb'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=eweb'>$ichr(eweb)</a></td>
<td ".($pSub=='align'?'class="act"':'')."><a href='tpl_doc.php?fid=$fid&pSub=align'>$itpl(align)</a></td>
<td ".($pSub=='common'?'class="act"':'')."><a href='tpl_doc.php?fid=$fid&pSub=common'>$itpl(common)</a></td>
</tr></table>";
?>
