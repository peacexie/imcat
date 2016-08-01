<?php
require(dirname(dirname(dirname(__FILE__))).'/run/_paths.php'); 
glbHtml::head('html');
$fid = basReq::val('fid','content');
$pSub = basReq::val('pSub','peace'); // peace,baidu,eweb //// peace,def

glbHtml::page('模版/特殊字符',1);
glbHtml::page('imadm'); //adm
echo basJscss::imp("/plus/editor/_pub.js");
echo basJscss::imp("/plus/editor/tpl_style.css");
glbHtml::page('body');

echo  "\n<table class='file_bar'><tr>
<td ".($pSub=='peace'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=peace'>特殊字符(peace)</a></td>
<td ".($pSub=='baidu'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=baidu'>特殊字符(baidu)</a></td>
<td ".($pSub=='eweb'?'class="act"':'')."><a href='tpl_char.php?fid=$fid&pSub=eweb'>特殊字符(eweb)</a></td>
<td ".($pSub=='align'?'class="act"':'')."><a href='tpl_doc.php?fid=$fid&pSub=align'>模版(align)</a></td>
<td ".($pSub=='common'?'class="act"':'')."><a href='tpl_doc.php?fid=$fid&pSub=common'>模版(common)</a></td>
</tr></table>";
?>
