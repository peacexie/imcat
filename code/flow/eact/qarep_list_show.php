<?php
(!defined('RUN_INIT')) && die('No Init');

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
basLang::inc('aflow', 'qarep_list');
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $cid = $idend = $r['cid'];
      if(empty($idfirst)) $idfirst = $cid;
      echo $cv->Select($cid);
      echo $cv->PTitle('faqs',$r['pid'],"/plus/ajax/redir.php?faqs.{$r['pid']}");
      echo $cv->Field($r['title'],1,48); 
      echo $cv->Field($r['mname']);
      echo $cv->Field($r['miuid']);
      echo $cv->Field($r['auser']);
      echo $cv->Time($r['atime']);
      echo $cv->Field($r['aip']);
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&cid=$r[cid]&recbk=ref","");
      echo "</tr>"; 
    }
    $dop->pgbar($idfirst,$idend);
}else{
    echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
