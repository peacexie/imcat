<?php
(!defined('RUN_MODE')) && die('No Init'); 

$dop->svPrep(); 
if(!empty($isadd)){ 
	$dop->svAKey();
	$dop->svPKey('add');
	$db->table($dop->tbid)->data($dop->fmv)->insert(); 
	$actm = '增加';
}else{ 
	$cid = $dop->svEKey();
	$dop->svPKey('edit');
	$db->table($dop->tbid)->data($dop->fmv)->where("cid='$cid'")->update();
	$actm = '修改';
}
basMsg::show("$actm 成功！");	
