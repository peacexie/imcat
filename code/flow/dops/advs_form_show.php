<?php
(!defined('RUN_MODE')) && die('No Init'); 

if(!empty($aid)){
	$fmo = $db->table($dop->tbid)->where("aid='$aid'")->find(); 
	$isadd = 0;
}else{
	$fmo = array();
	$isadd = 1;
}
$dop->fmo = $fmo; 
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
$dop->fmPKey();
fldView::lists($mod,$fmo);
$dop->fmProp();
glbHtml::fmae_send('bsend','提交');
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));