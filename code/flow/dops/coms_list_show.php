<?php
(!defined('RUN_MODE')) && die('No Init'); 

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
echo "<th>选</th><th>标题</th><th>显示</th><th>会员名称</th>"; 
echo "<th>电话</th><th>E-Mail</th><th>聊天号</th>";
echo "<th>添加</th><th>添加IP</th>"; 
echo "<th>修改</th>\n</tr>\n";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
	foreach($rs as $r){ 
	  $cid = $idend = $r['cid'];
	  if(empty($idfirst)) $idfirst = $cid;
	  echo $cv->Select($cid);
	  echo $cv->Field($r['title'],1,64); //echo $cv->Types($r['title']);
	  echo $cv->Show($r['show']);
	  echo $cv->Field($r['mname']);
	  echo $cv->Field($r['mtel']);
	  echo $cv->Field($r['memail']);
	  echo $cv->Field($r['miuid']);
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
