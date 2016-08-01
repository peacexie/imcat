<?php
(!defined('RUN_MODE')) && die('No Init');

$dop->svPrep();
if(!empty($isadd)){ // basReq::in()
	$dop->svAKey(); $did = $dop->fmu['did'] = $dop->fmv['did'];
	$db->table($dop->tbid)->data($dop->fmv)->insert(); 
	$db->table($dop->tbext)->data($dop->fmu)->insert(); 
	$actm = '增加';
}else{ 
	$did = $dop->svEKey();
	$db->table($dop->tbid)->data($dop->fmv)->where("did='$did'")->update();
	$dop->fmu['did'] = $did;
	$db->table($dop->tbext)->data($dop->fmu)->replace();
	$actm = '修改';
}
$dop->svEnd($did); //静态情况等
basMsg::show("$actm 成功！",'Redir');
