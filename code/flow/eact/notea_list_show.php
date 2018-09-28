<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA'); 

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->so->whrstr .= " AND `auser`='{$user->uinfo['uname']}'";
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
//basLang::inc('aflow', 'coms_list');
echo "<th>选</th><th>标题</th><th>显示</th><th>mname</th>
<th>类型</th><th>添加</th><th>添加IP</th><th>修改</th></tr>";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $cid = $idend = $r['cid'];
      if(empty($idfirst)) $idfirst = $cid;
      echo $cv->Select($cid);
      echo $cv->Field($r['title'],1,36); 
      echo $cv->Show($r['show']);
      echo $cv->Field($r['auser']);
      echo $cv->TKeys($r,1,'leixing',12);
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
