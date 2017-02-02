<?php
$_cbase['skip']['.none.'] = true;
require(dirname(__FILE__).'/_config.php');

$tpldir = $_cbase['tpl']['tpl_dir'] = req('tpldir');
$fjob = req('fjob'); 
$static = req('static'); // 后台静态管理/保存资料
$mkv = req('mkv');
$act = req('act');
$mod = req('mod');
//$q = $_SERVER['QUERY_STRING'];
$safix = $_cbase['safe']['safix'];
$sapp = basReq::ark($safix,'sapp'); 
$skey = basReq::ark($safix,'skey','Safe4'); 

// 处理语言
$lang = isset($_GET['lang']) ? $_GET['lang'] : $_cbase['sys']['lang'];
$lang && $_cbase['sys']['lang'] = $lang;

/*
### dops/dopBase.php :: function svEnd($id,$show=1)
- $js .= basJscss::jscode(0,PATH_ROOT."/plus/ajax/cron.php?static=updkid&tpldir=$tpl&mkv=$mkv");
### jslib\jsbext.js :: function jcronRun(tpldir,mkv,reurl)
- var url = '/plus/ajax/cron.php?tpldir='+tpldir+'&mkv='+mkv+'&'+_cbase.safil.url+'&'+jsRnd();
### admin/static.php :: 
- $cronurl = PATH_ROOT."/plus/ajax/cron.php";
*/

if($static){
    $user = user();
    if($user->userFlag!='Login') die('// NOT Login!');
}

if($static=='updkid'){ // 保存资料时执行
    echo "// ".vopStatic::toFile($mkv);
    die();
}elseif($static && $act=='mkv'){ // home,mlist,mdetail
    $msg = vopStatic::toFile($static); 
    echo lang('plus.cron_res')."$msg"; 
    basDebug::bugLogs('mkv-static',"$static@$tpldir",'detmp','db');
}elseif($static && $act=='add'){ // home,mlist,mdetail
    if($static=='home'){
        $msg = vopStatic::toFile('home'); 
        echo lang('plus.cron_res')."$msg"; 
    }elseif($static=='mlist'){
        $res = vopStatic::batList($mod,$tpldir); 
        vopStatic::showRes($res);
    }elseif($static=='mdetail'){
        $res = vopStatic::batDetail($mod,$tpldir); 
        vopStatic::showRes($res);
    }
    die();
}elseif($static && $act=='del'){
    if($static=='home'){
        $sfp = vopStatic::getPath('home','home',0);
        $msg = @unlink(DIR_HTML."/$sfp"); 
        echo $msg ? lang('plus.cron_ok')."$sfp!" : lang('plus.cron_err')."$sfp!"; 
    }elseif($static=='mlist'){
        $res = vopStatic::delList($mod,$tpldir); 
        vopStatic::showRes($res);
    }elseif($static=='mdetail'){
        $res = vopStatic::delDetail($mod,''); 
        vopStatic::showRes($res);
    }
    die();
}elseif(!empty($sapp) && !empty($skey) && !empty($fjob)){ // 指定计划任务
    $ocfgs = read('outdb','ex');
    $f1 = $sapp==$ocfgs['sign']['sapp'];
    $f2 = $skey==$ocfgs['sign']['skey'];
    if($f1 && $f2){
        $cron = new comCron($fjob);
    } 
}else{ // jcronRun-执行
    // comCron
    $chk = safComm::urlStamp('flag',90);
    $cron = new comCron();
    // comHooks
    $hook = comHook::listen($mkv,$tpldir);
    // vopStatic
    if($mkv){
        if(vopStatic::chkNeed($mkv)){
            echo "// ".vopStatic::toFile($mkv);
        }  
    } 
}

