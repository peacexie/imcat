<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA'); 

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
basLang::inc('aflow', 'coms_list');
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $cid = $idend = $r['cid'];
      $msg = empty($r['title']) ? $r['detail'] : $r['title'];
      if(empty($idfirst)) $idfirst = $cid;
      echo $cv->Select($cid);
      echo $cv->Field($msg,1,24); 
      echo $cv->Show($r['show']);
      echo $cv->Field($r['mname']);
      //echo $cv->Field($r['mtel']);
      //echo $cv->Field($r['memail']);
      //echo $cv->Field($r['miuid']);
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
