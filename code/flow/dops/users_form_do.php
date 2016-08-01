<?php
(!defined('RUN_MODE')) && die('No Init'); 

$dop->svPrep();
if(!empty($isadd)){ 
	$dop->svAKey();
	$dop->svAccount('add'); //basDebug::varShow($dop->fmv);
	$db->table($dop->tbid)->data($dop->fmv)->insert(); 
	$actm = '增加';
}else{ 
	$uid = $dop->svEKey();
	$dop->svAccount('edit');
	$db->table($dop->tbid)->data($dop->fmv)->where("uid='$uid'")->update();
	$actm = '修改';
}
$dop->svEnd($uid); //静态情况等
basMsg::show("$actm 成功！");	
