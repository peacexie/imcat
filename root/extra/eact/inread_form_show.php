<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

if(!empty($cid)){
    $fmo = $db->table($dop->tbid)->where("cid='$cid'")->find(); 
    $isadd = 0;
}else{
    $fmo = array();
    $isadd = 1;
}
$dop->fmo = $fmo;
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
$dop->fmPKey(0,0);
fldView::lists($mod,$fmo);
$dop->fmProp();
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));