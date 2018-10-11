<?php
namespace imcat;
$_cbase['tpl']['vdir'] = 'umc';
require dirname(dirname(__DIR__))."/run/_init.php";
require_once(DIR_VENDOR.'/a3rd/qqcAPI/qqConnectAPI.php');

$qc = new \QC();
$actoken = $qc->qq_callback();
$openid = $qc->get_openid(); 
$qc->setParms($actoken, $openid); //$qc = new QC($actoken,$openid);
$uinfo = $qc->get_user_info(); //array('nickname'=>'和平鸽@13');//
$db = db(); 

$nick = basStr::filTitle($uinfo["nickname"]); 
$sinfo = usrExtra::getUserOpenid($openid,'qq',1);
$logok = 0;
if(empty($sinfo)){
    $logok = 1;
    //add
    $uname = 'qq_'.usrExtra::fmtUserName($nick);
    $password = basKeyid::kidRand(0,6); // ??? show?
    $rins = usrMember::addUser('person',$uname,$password,$nick);
    usrMember::bindUser($uname,'qq',$openid);
}elseif(!empty($openid)){
    $logok = 1;
    //update
    $db->table('users_'.$sinfo['umods'])->data(array('mname'=>$nick))->where(array('uname'=>$sinfo['uname']))->update();
}

if($logok){
    usrExtra::setLoginLogger($openid,'qq');
    // dir
    $recbk = empty($_SESSION['recbk']) ? '' : $_SESSION['recbk'];
    $recbk || $recbk = surl('umc:0');
    $dir = "window.opener.location='$recbk';"; // window.opener.location.reload();
    $js = "$dir;setTimeout('window.close()',300);";
    echo basJscss::jscode($js); // header("Location:$recbk");
}else{
    basMsg::show("获取[qq-openid]错误!",'die');
}

/*

*/
