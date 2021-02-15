<?php 
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
//safComm::urlFrom();
glbHtml::head('html');

$act = req('act','chkVImg'); 
$mod = req('mod','','Key'); //basStr::filKey('');
$kid = basReq::ark('fm','kid','Key'); 
$uid = basReq::ark('fm','uid','Key'); 
$_groups = read('groups');

// 处理语言
$lang = req('lang',$_cbase['sys']['lang']);
$lang && $_cbase['sys']['lang'] = $lang;

switch($act){

case 'fsInit':
    safComm::urlStamp('check',30);
    $restr = safComm::formCInit();
    echo "document.write(\"$restr\");";
break;

case 'userExists':
    $key = req('key','uname');
    $val = req($key); $val || $val = basReq::ark('fm',$key);
    $res = usrMember::chkExists($key,$val,$mod);
    die($res);
break;

case 'fieldExists':
    admPFunc::fieldExists($kid,$mod,$_groups);
break;

case 'fieldCatid':
    admPFunc::fieldCatid($kid,$mod);
break;

case 'keyExists':
    admPFunc::keyExists($kid,$mod,$_groups);
break;

case 'modExists':
    if($re=basKeyid::keepCheck($kid,1,1,1)){ //$key,$chk,$fix,$grp
        die($re);
    }else{
        die("success");    
    }
break;

case 'infoRepeat': 
    admPFunc::infoRepeat($mod,$_groups);
break;

case 'chkVImg': // VImg
    safComm::chkVImg();
break;

case 'cfield':
    admPFunc::cfield($kid,$mod);
break;

case 'uLogin':
    $uname = req('uname'); $umod = req('umod');
    $uadm = user('Admin'); $run = $_cbase['run'];
    if($uadm->userFlag=='Login'){
        //usrBase::setLogin('m',$uname);
        //header("Location:".surl("umc:0"));
        $ckey = usrMember::getCkey('login-uio'); 
        $rlog = ['ckey'=>$ckey, 'utype'=>'idpwd', 'atime'=>$run['stamp'], 'aip'=>$run['userip']];
        //dump($rlog);
        usrMember::loginUser($rlog, $uname, $umod);
        header("Location:".surl("hi:login"));
    }else{
        echo "(uname=$uname)";    
    }
break;

case 'chku_exist':
    //
break;

default:
    exit('Empty action!');     
}//end switch

/*

*/
