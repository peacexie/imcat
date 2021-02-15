<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
echo "<tr><th>选</th><th>编号</th><th>客户名称</th><th>联系人</th>
  <th>电话</th><th>设备</th><th>添加</th><th>修改</th></tr>";
// <th>显示</th> <th>添加IP</th><th>修改</th> <th>设备类型</th>
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $did = $idend = $r['did'];
      $msg = empty($r['title']) ? $r['detail'] : $r['title'];
      if(empty($idfirst)) $idfirst = $did;
      echo $cv->Select($did);
      echo $cv->Field($r['csno'],1,24); 
      //echo $cv->TKeys($r,1,'equip',12);
      echo $cv->Field($r['title'],1,24); 
      echo $cv->Field($r['mname'],1,24); 
      //echo $r['pflag'] ? $cv->TKeys($r,1,'pflag',12) : "<td class=tc>(草稿)</td>";
      echo $cv->Field($r['mtel'],1,24); 
      echo $cv->Url('设备',1,"?dops-a&mod=cslogs&pid=$r[did]","");
      //echo $cv->Show($r['show']);
      //echo $cv->Field($r['mname'],1,48);
      //echo $cv->Field($r['mtel']);
      //echo $cv->Field($r['memail']);
      //echo $cv->Field($r['miuid']);
      echo $cv->Time($r['atime']);
      //echo $cv->Field($r['aip']);
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&did=$r[did]&recbk=ref","");
      echo "</tr>"; 
    }
    $dop->pgbar($idfirst,$idend);
}else{
    echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
