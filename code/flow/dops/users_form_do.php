<?php
(!defined('RUN_DOPA')) && die('No DopA');

$dop->svPrep();
if(!empty($isadd)){ 
    $dop->svAKey();
    $dop->svAccount('add'); //basDebug::varShow($dop->fmv);
    $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    $actm = lang('flow.dops_add');
}else{ 
    $uid = $dop->svEKey();
    $dop->svAccount('edit');
    $db->table($dop->tbid)->data($dop->fmv)->where("uid='$uid'")->update();
    $actm = lang('flow.dops_edit');
}
$dop->svEnd($uid); //静态情况等
basMsg::show("$actm".lang('flow.dops_ok'));    
