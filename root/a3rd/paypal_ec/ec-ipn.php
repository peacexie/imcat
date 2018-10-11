<?php
$logfid = 8; //date('d-His');
require __DIR__.'/ec-cfg.php';

$item_name = req('item_name'); //171104-HCV604 / 2017-3s-rkv01
$mc_gross = req('mc_gross'); //252.28
$custom = req('custom'); //dump("$item_name.$custom");

$logfid = date('d-His'); // $item_name, 8, date('d-His');
exvOpay::payDebug('paypal', $logfid);

//dump($_GET);
//dump($_POST);

if(empty($custom) || empty($item_name)){
    die('Null Params!');
}  

$_tmp = comConvert::sysRevert($custom,1); 
$_arr = explode('.',$_tmp); 
if(count($_arr)!=3 || $_arr[0]!=$item_name && time()-intval($_arr[1])>3600){
    die('Error Params!');
}

$order = $db->table('coms_corder')->where("cid='{$item_name}'")->find();
if(empty($order) || $order['eip']!=$_arr[2]){
    die("Error [$item_name]");
}

$fm = array();

// update:ordstps
$_act = $mc_gross<0 ? 'Refund' : 'PayOK';
$fm['ordstps'] = devOcar::setOrdstps($order['ordstps'], $_act);

if(empty($order['mname']))     $fm['mname']     = req('first_name');
if(empty($order['mtitle']))    $fm['mtitle']    = req('last_name');
if(empty($order['memail']))    $fm['memail']    = req('payer_email');

if(empty($order['maddr']))     $fm['maddr']     = req('address_street');
if(empty($order['maddr2']))    $fm['maddr2']    = req('address_name');

if(empty($order['mcity']))     $fm['mcity']     = req('address_city');
if(empty($order['mprovince'])) $fm['mprovince'] = req('address_state');

if(empty($order['mpcode']))    $fm['mpcode']    = req('address_zip');
if(empty($order['mstate']))    $fm['mstate']    = req('address_country').'('.req('address_country_code').')';

$db->table('coms_corder')->data(in($fm))->where("cid='{$item_name}'")->update(0);
//dump($fm);
