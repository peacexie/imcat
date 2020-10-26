<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

#$dop->order = 'click-a';
$dop->sobar($dop->msgBar($msg)); 
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
#basLang::inc('aflow', 'cargo_list'); 
echo "<th>选</th><th>ID</th><th>标题</th><th>栏目</th><th>显示</th>
<th>排序</th><th>添加时间</th><th>修改时间</th><th>修改</th><th>复制</th></tr>";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs('', 0, 'catid, click')){ 
    foreach($rs as $r){ 
      $did = $idend = $r['did'];
      if(empty($idfirst)) $idfirst = $did;
      echo "<tr>\n";
      echo $cv->Select($did);
      echo $cv->Field($r['did']);
      echo $cv->Title($r,1,'title',"");
      echo $cv->Types($r['catid']);
      echo $cv->Show($r['show']);
      echo $cv->Field($r['click']);
      echo $cv->Time($r['atime']);
      //echo $cv->Field($r['auser']);
      echo $cv->Time($r['etime'],'y');
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&did=$r[did]&recbk=ref","");
      echo $cv->Url(lang('flow.dops_copy'),1,"?binc-exd_copy&mod=$mod&kid=$r[did]&title=$r[title]",lang('flow.dops_cpro'),480,360);
      echo "</tr>"; 
    }
    $dop->pgbar($idfirst,$idend);
}else{
    echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
