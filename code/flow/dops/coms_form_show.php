<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA');

if(!empty($cid)){
    $fmo = $db->table($dop->tbid)->where("cid='$cid'")->find(); 
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
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));