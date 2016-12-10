<?php
(!defined('RUN_DOPA')) && die('No DopA'); 

if(!empty($uid)){
	$fmo = $db->table($dop->tbid)->where("uid='$uid'")->find(); 
	$fme = $db->table($dop->tbuacc)->where("uid='$uid'")->find();
	$fme && $fmo = $fmo + $fme;
	$isadd = 0;
}else{
	$fmo = array();
	$isadd = 1;
}
$dop->fmo = $fmo;
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
$dop->fmAccount();
fldView::lists($mod,$fmo);
$dop->fmProp();
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));
if(in_array($mod,array('company','govern','organize'))){
	fldView::relat("fm[grade]","fm[miuid],$mod,$uid,fm[grade]"); 
}
