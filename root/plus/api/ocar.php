<?php
require(dirname(__FILE__).'/_config.php'); 

$act = req('act','shipfee');
safComm::urlFrom();

if($act=='shipfee'){
    glbHtml::head();
    $from = req('from','');
    $to = req('to','');
    $weight = req('weight','0.1','N');
    $data = exvOcar::shipfee($from,$to,$weight); 
    if(strlen(req('debug'))) print_r($data);
    $data = comParse::jsonEncode($data);
    die("var data = $data;");
}elseif($act=='ordstat'){
    $db = db();
    $ordid = req('ordid');
    $row = $db->table('plus_paylog')->where("ordid='$ordid' AND stat='success'")->find();
    $db->table('coms_corder')->data(array('ordstat'=>'paid'))->where("cid='$ordid' AND ordstat='new'")->update(); 
    die("var data = '".(empty($row) ? '' : 'YES')."'");    
}
