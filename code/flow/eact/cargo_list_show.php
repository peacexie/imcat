<?php
(!defined('RUN_MODE')) && die('No Init'); 

$dop->sobar($dop->msgBar($msg)); 
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
echo "<th>选</th><th>标题</th><th>栏目</th><th>显示</th>"; 
echo "<th>添加时间</th><th>修改时间</th>"; 
echo "<th>修改</th><th>复制</th>\n</tr>\n";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
	foreach($rs as $r){ 
	  $did = $idend = $r['did'];
	  if(empty($idfirst)) $idfirst = $did;
	  echo "<tr>\n";
	  echo $cv->Select($did);
	  echo $cv->Title($r,1,'title',"");
	  echo $cv->Types($r['catid']);
	  echo $cv->Show($r['show']);
	  echo $cv->Time($r['atime']);
	  //echo $cv->Field($r['auser']);
	  echo $cv->Time($r['etime'],'y');
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&did=$r[did]&recbk=ref","");
	  echo $cv->Url('复制',1,"?file=binc/exd_copy&mod=$mod&kid=$r[did]&title=$r[title]",'复制产品',480,360);
	  echo "</tr>"; 
	}
	$dop->pgbar($idfirst,$idend);
}else{
	echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
