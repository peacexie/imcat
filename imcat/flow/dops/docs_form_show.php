<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA'); 
 
if(!empty($did)){
    $fmo = $db->table($dop->tbid)->where("did='$did'")->find();
    $fme = $dop->tbext ? $db->table($dop->tbext)->where("did='$did'")->find() : [];
    $fme && $fmo = $fmo + $fme;
    $isadd = 0;
}else{
    $fmo = array();
    $isadd = 1;
}
$dop->fmo = $fmo;
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
$dop->fmCatid();
fldView::lists($mod,$fmo);
$dop->fmProp();
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));
// 扩展示例
if($mod=='cargo'){
    fldView::relat("relpb,fm[catid],fm[brand]","fm[xinghao],$mod,$did"); 
}
if($mod=='keres'){
    fldView::relat("relyc,fm[ygrade],fm[course]");
}
if(in_array($mod,array('about','demo'))){
    fldView::relat("fm[catid]","fm[catid],$mod,$did");
}