<?php
(!defined('RUN_MODE')) && die('No Init'); 
 
if(!empty($did)){
	$fmo = $db->table($dop->tbid)->where("did='$did'")->find();
	$fme = $db->table($dop->tbext)->where("did='$did'")->find();
	$fme && $fmo = $fmo + $fme;
	$isadd = 0;
}else{
	$fmo = array();
	$isadd = 1;
}
$dop->fmo = $fmo;
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
glbHtml::fmae_row(lang('flow.dops_icat'),$dop->fmType('catid').' &nbsp; '.lang('flow.dops_ishow').$dop->fmShow());
fldView::lists($mod,$fmo);
$dop->fmProp();
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));
//$jchg = "relCatid('fm[ftype]','$mod','$uname','fm[grade]');";
//echo basJscss::jscode("\n $jchg \$(jsElm.jeID('fm[grade]')).change(function(){ $jchg; });");
//fldView::relat("relyc,fm[ygrade],fm[course]");
fldView::relat("fm[catid]","fm[catid],$mod,$did");
//fldView::relat("fm[grade]","fm[ftype],$mod,$uname,fm[grade]"); 
//fldView::relat("relpb,fm[catid],fm[brand]","fm[xinghao],$mod,$did"); 	
