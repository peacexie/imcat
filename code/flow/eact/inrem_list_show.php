<?php
(!defined('RUN_MODE')) && die('No Init'); 

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
echo "<th>选</th><th>公文标题</th><th>评论标题</th><th>帐号</th>"; 
echo "<th>首次阅读</th><th>首次IP</th>"; 
echo "<th>修改</th>\n</tr>\n";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
	foreach($rs as $r){ 
	  $cid = $idend = $r['cid'];
	  if(empty($idfirst)) $idfirst = $cid;
	  echo $cv->Select($cid);
	  echo $cv->PTitle('indoc',$r['pid'],"/plus/ajax/redir.php?indoc.{$r['pid']}");
	  echo $cv->Field($r['title'],1,48); 
	  echo $cv->Field($r['auser']);
	  echo $cv->Time($r['atime']);
	  echo $cv->Field($r['aip']);
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&cid=$r[cid]&recbk=ref","");
	  echo "</tr>"; 
	}
	$dop->pgbar($idfirst,$idend);
}else{
	echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
