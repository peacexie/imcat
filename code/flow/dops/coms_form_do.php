<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA');

$dop->svPrep(); 
if(!empty($isadd)){ 
    $dop->svAKey();
    $dop->svPKey('add');
    $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    $actm = lang('flow.dops_add');
}else{ 
    $cid = $dop->svEKey();
    $dop->svPKey('edit');
    $db->table($dop->tbid)->data($dop->fmv)->where("cid='$cid'")->update();
    $actm = lang('flow.dops_edit');
}
basMsg::show("$actm".lang('flow.dops_ok'));    
