<?php
$_mod = basename(__FILE__,'.php');
require(dirname(__FILE__).'/_cfgcom.php'); 

# setting
$gap = 5; //5分钟间隔
$mod = 'inread'; $pmod = 'indoc';
$tbid = "coms_$mod";

# check
$pid = req('pid');
if(!$pid) die();
$chk = safComm::urlStamp('flag',90);
$chk && die("//$chk");

$user = user('Member'); 
if($user->userFlag!='Login') die();


# read
$uname = $user->uinfo['uname'];
$pwhr = "pid='$pid' AND auser='$uname'";

$fmc = $db->table($tbid)->where($pwhr)->find(); 
if($fmc){
	if($_cbase['run']['stamp']-$fmc['etime']<$gap*60) die();
	$data = array(
		'eip' => $_cbase['run']['userip'],
		'etime' => $_cbase['run']['stamp'],
		'euser' => $uname,
		'readcnt' => $fmc['readcnt']+1,
	);
	$db->table($tbid)->data($data)->where($pwhr)->update(); 
}else{
	$kar = glbDBExt::dbAutID($tbid,'yyyy-md-','31');
	$data = array(
		'aip' => $_cbase['run']['userip'],
		'atime' => $_cbase['run']['stamp'],
		'auser' => $uname,
		'cid' => $kar[0],
		'cno' => $kar[1],
		'show'=> '1',
		'pid' => $pid,
		'readcnt' => '1',
	);	
	$db->table($tbid)->data($data)->insert(); 
}

