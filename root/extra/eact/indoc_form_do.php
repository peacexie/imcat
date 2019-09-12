<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$dop->svPrep();
tex('texIndoc','umc')->fixIspub($dop,$isadd); // topub=ispub : 扩展
if(!empty($isadd)){ 
    $dop->svAKey(); $did = $dop->fmu['did'] = $dop->fmv['did'];
    $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    $db->table($dop->tbext)->data($dop->fmu)->insert(0); 
    $actm = lang('flow.dops_add');
}else{ 
    $did = $dop->svEKey();
    $db->table($dop->tbid)->data($dop->fmv)->where("did='$did'")->update();
    $dop->fmu['did'] = $did;
    $db->table($dop->tbext)->data($dop->fmu)->replace(0);
    $actm = lang('flow.dops_edit');
}
$dop->svEnd($did); //静态情况等
tex('texIndoc','umc')->exNotice($dop,$isadd); // 通知扩展
basMsg::show("$actm".lang('flow.dops_ok'),'Redir');
