<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$pid = req('pid');
if($pid){
    $dop->so->whrstr .= " AND `pid`='$pid'";
}
$lnkadd = $dop->cv->Url('[新增记录]'.'&gt;&gt;',0,"?$mkv&mod=$mod&pid=$pid&view=form&recbk=ref",""); // &stype=$stype
$dop->sobar($dop->msgBar($msg,$lnkadd));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
echo "<tr><th>选</th><th>流水号</th><th>本次状态</th><th>处理日志</th><th>处理人</th><th>添加</th><th>添加IP</th><th>修改</th></tr>";
// <th>显示</th>
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $cid = $idend = $r['cid'];
      //$msg = empty($r['title']) ? $r['detail'] : $r['title'];
      $tabMflag = basElm::text2arr('cstask.mflag');
      $mflag = isset($tabMflag[$r['mflag']]) ? $tabMflag[$r['mflag']] : $r['mflag'];
      if(empty($idfirst)) $idfirst = $cid;
      echo $cv->Select($cid);
      echo $cv->Field($r['cid'],1,24); 
      echo $cv->Field($mflag,1,24);
      echo $cv->Field($r['title'],1,36); 
      //echo $r['pflag'] ? $cv->TKeys($r,1,'pflag',12) : "<td class=tc>(草稿)</td>";
      //echo $cv->TKeys($r,1,'sflag',12);
      //echo $cv->Show($r['show']);
      echo $cv->Field($r['mname'],1,48);
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
