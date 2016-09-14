<?php
require(dirname(__FILE__).'/_config.php'); 

$act = basReq::val('act','shipfee');
safComm::urlFrom();

if($act=='shipfee'){
	glbHtml::head();
	$from = basReq::val('from','');
	$to = basReq::val('to','');
	$weight = basReq::val('weight','0.1','N');
	//echo "$from,$to,$weight";
	$data = exvOcar::shipfee($from,$to,$weight); 
	if(strlen(basReq::val('debug'))) print_r($data);
	$data = comParse::jsonEncode($data);
	die("var data = $data;");
}elseif($act=='ordstat'){
	$db = glbDBObj::dbObj();
	$ordid = basReq::val('ordid');
	$row = $db->table('plus_paylog')->where("ordid='$ordid' AND stat='success'")->find();
	$db->table('coms_corder')->data(array('ordstat'=>'paid'))->where("cid='$ordid' AND ordstat='new'")->update(); 
	die("var data = '".(empty($row) ? '' : 'YES')."'");	
}
