<?php
require dirname(dirname(dirname(__FILE__)))."/run/_init.php";
require_once(DIR_VENDOR.'/a3rd/qqcAPI/qqConnectAPI.php');

$qc = new QC();
$actoken = $qc->qq_callback();
$openid = $qc->get_openid(); //'s4_1234';
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
    usrMember::bindUser($uname,'qq',$openid); // $mname,$pptmod,$pptuid
}elseif(!empty($openid)){
    $logok = 1;
    //update
    $db->table('users_'.$sinfo['umods'])->data(array('mname'=>$nick))->where(array('uname'=>$sinfo['mname']))->update();
}

if($logok){
    usrExtra::setLoginLogger($openid,'qq');
    // dir
    $recbk = $_SESSION['recbk'];
    $recbk || $recbk = surl('umc:0');
    header("Location:$recbk");
}else{
    basMsg::show("获取[qq-openid]错误!",'die');
}

/*

echo "Gender:".$arr["gender"];
echo "NickName:".$arr["nickname"];
echo "<img src=\"".$arr['figureurl']."\">";
echo "<img src=\"".$arr['figureurl_1']."\">";
echo "<img src=\"".$arr['figureurl_2']."\">";
echo "vip:".$arr["vip"];
echo "level:".$arr["level"];
echo "is_yellow_year_vip:".$arr["is_yellow_year_vip"];

*/
