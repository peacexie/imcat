<?php
(!defined('RUN_MODE')) && die('No Init'); 

$dop->svPrep(); 
if(!empty($isadd)){ 
	$dop->svAKey();
	$dop->svPKey('add');
	$db->table($dop->tbid)->data($dop->fmv)->insert(); 
	$actm = '增加';
	$aid = $dop->fmv['aid'];
}else{ 
	$aid = $dop->svEKey();
	$dop->svPKey('edit');
	$db->table($dop->tbid)->data($dop->fmv)->where("aid='$aid'")->update();
	$actm = '修改';
}
vopStatic::advMod($mod,$aid);
basMsg::show("$actm 成功！");	
