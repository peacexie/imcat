<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA');;

$dop->svPrep(); 
if(!empty($isadd)){ 
    $dop->svAKey();
    $dop->svPKey('add');
    $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    $actm = lang('flow.dops_add');
    $aid = $dop->fmv['aid'];
}else{ 
    $aid = $dop->svEKey();
    $dop->svPKey('edit');
    $db->table($dop->tbid)->data($dop->fmv)->where("aid='$aid'")->update();
    $actm = lang('flow.dops_edit');
}
vopStatic::advMod($mod,$aid);
basMsg::show("$actm".lang('flow.dops_ok'));    
