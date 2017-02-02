<?php
(!defined('RUN_DOPA')) && die('No DopA');

if(req('umod')=='upd'){
    echo vopStatic::advMod($mod,"(all)");
    echo "<p class='tc'><a href='?file=$file&mod=$mod&view=list'>".lang('flow.dops_back')."</a></p>";
}

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
basLang::inc('aflow', 'advs_list', $dop->cfg);
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $aid = $idend = $r['aid'];
      if(empty($idfirst)) $idfirst = $aid;
      $kcu = $dop->cfg['etab']==4 ? 'auser' : 'click';
      echo $cv->Select($aid);
      echo $cv->Title($r,1,'title',$dop->avLink($r),64);
      echo $cv->Types($r['catid']);
      echo $cv->Show($r['show']);
      echo $cv->Url('Url',1,"$r[url]","blank");
      echo $cv->Field($r[$kcu]);
      echo $cv->Time($r['atime']);
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&aid=$r[aid]&recbk=ref","");
      echo "</tr>"; 
    }
    $dop->pgbar($idfirst,$idend);
}else{
    echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
