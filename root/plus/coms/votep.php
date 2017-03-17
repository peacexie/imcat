<?php
$_mod = basename(__FILE__,'.php');
require(dirname(__FILE__).'/_cfgcom.php'); 

if(empty($bsend)){ die('Empty!'); }
dopCheck::headComm();

$pinfo = dopFunc::getMinfo('votes',$pid); 
$stamp = time(); 
if(empty($pinfo)){
    basMsg::show('资料错误！','Redir');
}
if(!empty($pinfo['vstart'])&&$pinfo['vstart']>$stamp){ 
    basMsg::show('还未开始！','Redir');
}
if(!empty($pinfo['vend'])&&$pinfo['vend']<$stamp){ 
    basMsg::show('已经过期！','Redir');
}
if(!empty($pinfo['vmode'])&&count($fm)>$pinfo['vmode']){
    basMsg::show('选资料太多！','Redir');
}

dopCheck::headComm();
// ip, js-max

$dstr = '';
foreach ($fm as $cid) {
    $tab = $db->table('coms_votei',2);
    $cid = basStr::filKey($cid,'._-');
    $db->query("UPDATE $tab SET vcnt=vcnt+1 WHERE cid='$cid'");
    $dstr .= (empty($dstr)?'':',').$cid;
}
$kar = glbDBExt::dbAutID('coms_votep','yyyy-md-',31);
$data = array('detail'=>$dstr,'cid'=>$kar[0],'cno'=>$kar[1],'pid'=>$pid);
$db->table('coms_votep')->data($data)->insert(); 
basMsg::show('感谢投票！','Redir');

/*
  'vstart' => '1480167366',
  'vend' => '1764164166',
  'vmode' => '3',
*/
