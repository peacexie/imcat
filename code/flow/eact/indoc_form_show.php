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
if(empty($fmo['author'])){
	$fmo['author'] = $user->uinfo['mname'];
}
if(empty($fmo['indep'])){
	$fmo['indep'] = @$user->uinfo['intech'];	
}
$dop->fmo = $fmo;
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
glbHtml::fmae_row(lang('flow.dops_icat'),$dop->fmType('catid').' &nbsp; '.lang('flow.dops_ishow').$dop->fmShow());
fldView::lists($mod,$fmo);
$dop->fmProp();
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));