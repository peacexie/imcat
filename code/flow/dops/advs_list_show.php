<?php
(!defined('RUN_MODE')) && die('No Init'); 

if(basReq::val('umod')=='upd'){
	echo vopStatic::advMod($mod,"(all)");
    echo "<p class='tc'><a href='?file=$file&mod=$mod&view=list'>返回</a></p>";
}

//$dop->dskey  = $dop->so->dskey  = 'mtel'; //改变默认搜索字段
$dop->sobar($dop->msgBar($msg));
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
echo "<th>选</th><th>标题</th><th>栏目</th><th>显示</th>"; 
echo "<th>Url</th><th>".($dop->cfg['etab']==4 ? '用户' : '点击')."</th>";
echo "<th>添加</th><th>修改</th>\n</tr>\n";
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
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&aid=$r[aid]&recbk=ref","");
	  echo "</tr>"; 
	}
	$dop->pgbar($idfirst,$idend);
}else{
	echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));
