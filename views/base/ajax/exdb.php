<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
$_cbase['skip']['.none.'] = true;

$ocfgs = read('outdb','ex');

$safix = $_cbase['safe']['safix']; 
$sapp = basReq::ark($safix,'sapp'); 
$skey = basReq::ark($safix,'skey','Safe4');
$act = req('act','pull'); //pull,show,psyn,crawl,oimp,
$method = 'exd'.ucfirst($act);

if(!empty($sapp) && !empty($skey)){
    $f1 = $sapp==$ocfgs['sign']['sapp'];
    $f2 = $skey==$ocfgs['sign']['skey'];
    $chk = ($f1 && $f2) ? '' : 'error';
}else{
    $chk = safComm::urlStamp('flag',90);
}
$chk && die("$chk"); //error
$mod = req('mod');
$debug = req('debug'); //links,field
$sysid = req('sysid');
$job = req('job');


/*
2018-03-31#del#oimp,psyn
*/


new exaCrawl(); //加载采集/导入扩展方法
$exd = new exdFunc($mod); 
if(in_array($act,array('pull','show'))){ 
    $res = $exd->$method();
    echo $res;
}elseif(in_array($act,array('crawl')) && !empty($debug)){ // 'oimp',
    $jcfg = exdBase::getJCfgs($act,$job);
    $method = "{$method}_Debug";
    $res = $exd->$method($jcfg,$debug); 
    $exd->showBug($res,$exd,$debug);
}elseif(in_array($act,array('crawl')) && !empty($sysid)){ // 'oimp',
    $jcfg = exdBase::getJCfgs($act,$job);
    $method = "{$method}_Update";
    $res = $exd->$method($jcfg,$sysid); 
    $exd->showRes($res);
}elseif(in_array($act,array('crawl'))){ // 'psyn','oimp',
    $jcfg = exdBase::getJCfgs($act,$job);
    $res = $exd->$method($jcfg);
    $exd->showRes($res);
}

