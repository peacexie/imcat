<?php
(!defined('RUN_DOPA')) && die('No DopA');

$dop->svPrep();
if(!empty($isadd)){ // basReq::in() 
    $dop->svAKey(); $did = $dop->fmu['did'] = $dop->fmv['did'];
    $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    $db->table($dop->tbext)->data($dop->fmu)->insert(0); 
    $actm = lang('flow.dops_add');
}else{ 
    $did = $dop->svEKey();
    $db->table($dop->tbid)->data($dop->fmv)->where("did='$did'")->update();
    $dop->fmu['did'] = $did;
    if($db->table($dop->tbext)->where("did='$did'")->find()){
        $db->table($dop->tbext)->data($dop->fmu)->where("did='$did'")->update(0);
    }else{
        $db->table($dop->tbext)->data($dop->fmu)->insert(0);
    }
    $actm = lang('flow.dops_edit');
}
$dop->svEnd($did); //静态情况等
basMsg::show("$actm".lang('flow.dops_ok'),'Redir');
