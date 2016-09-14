<?php
(!defined('RUN_MODE')) && die('No Init'); 
// 参考: dops/docs_list_show.php

$sbar = "\n".$so->Type(90,lang('flow.op0_cat')); // for:demo
$sbar .= "\n&nbsp; ".$so->Field('mfrom',70);
$sbar .= "\n&nbsp; ".$so->Field('mfron',70);
$sbar .= "\n&nbsp; ".$so->Field('mwork',70);
$sbar .= "\n&nbsp; ".$so->Word(80,80,lang('flow.op0_filt'));
$sbar .= "<br />\n".$so->Show(60);
$sbar .= "\n&nbsp; ".$so->Field('tphot',70);
$sbar .= "\n&nbsp; ".$so->Field('tpfields',70);
$sbar .= "\n&nbsp; ".$so->Area(70,80);
$sbar .= "\n&nbsp; ".$so->Order(array('did' => 'ID(D)','did-a' => 'ID(A)',),70);
$so->Form($sbar,$dop->msgBar($msg),30);
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
basLang::inc('aflow', 'demo_list');
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
	foreach($rs as $r){ 
	  $did = $idend = $r['did'];
	  if(empty($idfirst)) $idfirst = $did;
	  echo "<tr>\n".$cv->Select($did);
	  $r_text  = ' ['.$cv->TKeys($r,0,'mfrom',12,'-').'] ';
	  $r_text .= ' ['.$cv->TKeys($r,0,'mfron',12,'-').'] '; 
	  $r_text .= ' ['.$cv->TKeys($r,0,'mwork',12,'-').'] ';
	  $r_text .= ' ['.$cv->TKeys($r,0,'tpfields',12,'-').'] ';
	  echo "<td class='tl'>".$cv->Title($r,0,'title',"")."<div class='tr'>[".lang('flow.dops_rem')."]$r_text</div>\n</td>\n";
	  echo $cv->Types($r['catid']);
	  echo $cv->Show($r['show']);
	  echo "<td class='tc'>".$cv->Time($r['atime'],0,'y')."<br>".$cv->Field($r['auser'],0)."</td>\n";
	  echo "<td class='tc'>".$cv->Time($r['etime'],0)."<br>".$cv->Field($r['eip'],0,8)."</td>\n";
	  echo "<td class='tc'>".$cv->Field($r['mtel'],0,9)."<br>".$cv->Time($r['dend'],0,'',1)."</td>\n";
	  echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&did=$r[did]&recbk=ref","");
	  echo "</tr>"; 
	}
	$dop->pgbar($idfirst,$idend);
}else{
	echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
