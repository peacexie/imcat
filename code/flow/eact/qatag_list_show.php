<?php
(!defined('RUN_MODE')) && die('No Init'); 

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
echo "<th>选</th><th>标签</th><th>热度</th>"; 
echo "<th>添加</th><th>IP</th>"; 
echo "<th>更新</th><th>IP</th>"; 
echo "<th>修改</th>\n</tr>\n";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
	foreach($rs as $r){ 
	  $cid = $idend = $r['cid'];
	  if(empty($idfirst)) $idfirst = $cid;
	  echo $cv->Select($cid);
	  echo $cv->Field($r['title']);
	  echo $cv->Field($r['hotcnt']); 
	  echo $cv->Time($r['atime']);
	  echo $cv->Field($r['aip']);
	  echo $cv->Time($r['etime']);
	  echo $cv->Field($r['eip']);
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&cid=$r[cid]&recbk=ref","");
	  echo "</tr>"; 
	}
	$dop->pgbar($idfirst,$idend);
}else{
	echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
