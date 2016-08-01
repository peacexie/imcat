<?php
(!defined('RUN_MODE')) && die('No Init'); 

if($mod=='adminer') $dop->so->whrstr .= " AND `aip` !='disturb'";
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
echo "<th>选</th><th>账号</th><th>等级</th>"; 
echo "<th>名称</th><th>电话</th><th>E-Mail</th><th>聊天号</th>";
echo "<th>注册</th><th>注册IP</th>"; 
echo "<th>修改</th>\n</tr>\n";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
	foreach($rs as $r){ 
	  $uid = $idend = $r['uid'];
	  if(empty($idfirst)) $idfirst = $uid;
	  echo $cv->Select($uid);
	  echo $cv->Url($r['uname'],1,PATH_ROOT."/plus/ajax/cajax.php?act=uLogin&uname={$r['uname']}","blank"); 
	  echo $cv->Types($r['grade']);
	  echo $cv->Field($r['mname']);
	  echo $cv->Field($r['mtel']);
	  echo $cv->Field($r['memail']);
	  echo $cv->Field($r['miuid']);
	  echo $cv->Time($r['atime']);
	  echo $cv->Field($r['aip']);
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&uid=$r[uid]&recbk=ref","");
	  echo "</tr>"; 
	}
	$dop->pgbar($idfirst,$idend);
}else{
	echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
