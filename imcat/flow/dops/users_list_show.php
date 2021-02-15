<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA');

if($mod=='adminer') $dop->so->whrstr .= " AND `aip` !='disturb'";
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
basLang::inc('aflow', 'users_list');
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $uid = $idend = $r['uid'];
      if(empty($idfirst)) $idfirst = $uid;
      echo $cv->Select($uid);
      echo $cv->Url($r['uname'],1,PATH_BASE."?ajax-cajax&act=uLogin&uname={$r['uname']}&umod=$mod","blank"); 
      echo $cv->Types($r['grade']);
      echo $cv->Field($r['mname']);
      echo $cv->Show($r['show']);
      echo $cv->Field($r['mtel']);
      echo $cv->Field($r['memail']);
      echo $cv->Field($r['miuid']);
      echo $cv->Time($r['atime']);
      echo $cv->Field($r['aip']);
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&uid=$r[uid]&recbk=ref","");
      echo "</tr>"; 
    }
    $dop->pgbar($idfirst,$idend);
}else{
    echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
