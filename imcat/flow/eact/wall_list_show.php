<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
//basLang::inc('aflow', 'inrem_list');
echo "<th>选</th><th>文本内容</th><th>添加时间</th>
<th>color</th><th>digg</th><th>修改</th></tr>";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $cid = $idend = $r['cid'];
      if(empty($idfirst)) $idfirst = $cid;
      echo $cv->Select($cid);
      echo "<td class='tl'>".basStr::cutWidth($r['title'],48,'..')."</td>\n";
      echo $cv->Time($r['atime']);
      echo $cv->Field(str_replace(array('text-','bg-'),'',$r['vtxt'].'/'.$r['vbg']),1,24);
      echo $cv->Field($r['diggtop'].'/'.$r['diggdown']);
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&cid=$r[cid]&recbk=ref","");
      echo "</tr>"; 
    }
    $dop->pgbar($idfirst,$idend);
}else{
    echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
