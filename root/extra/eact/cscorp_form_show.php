<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA'); 
 
if(!empty($did)){
    $fmo = $db->table($dop->tbid)->where("did='$did'")->find();
    $fme = $dop->tbext ? $db->table($dop->tbext)->where("did='$did'")->find() : [];
    $fme && $fmo = $fmo + $fme;
    $isadd = 0;
    $csno = $fmo['csno'];
}else{
    $fmo = array();
    $isadd = 1;
    $csno = '';
}
$dop->fmo = $fmo;
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
$dop->fmCatid();
fldView::lists($mod,$fmo);
$dop->fmProp();
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd","csno|$csno"));

