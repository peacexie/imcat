<?php
(!defined('RUN_MODE')) && die('No Init'); 

$dop->sobar($dop->msgBar($msg)); 
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
basLang::inc('aflow', 'indoc_list');
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
	foreach($rs as $r){ 
	  $did = $idend = $r['did'];
	  if(empty($idfirst)) $idfirst = $did;
	  echo "<tr>\n";
	  echo $cv->Select($did);
	  echo $cv->Title($r,1,'title',"");
	  echo $cv->Types($r['catid']);
	  echo $cv->TKeys($r,1,'indep');
	  echo $cv->TKeys($r,1,'rdtip',2,'',1);  
	  echo $cv->Show($r['show']);
	  echo $cv->Time($r['atime']);
	  echo $cv->Field($r['auser']);
	  echo $cv->Time($r['etime'],'y');
	  echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&did=$r[did]&recbk=ref","");
	  echo "</tr>"; 
	}
	$dop->pgbar($idfirst,$idend);
}else{
	echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
