<?php 
require(dirname(__FILE__).'/_config.php');

$qstr = $_SERVER['QUERY_STRING'];
// advs:mod.key

if(strpos($qstr,':')>0){
    $a = explode(':',$qstr);
    $act = $a[0];
    $mkv = $a[1];
}else{
    $act = 'defdir';
    $mkv = $qstr;
}
if(strpos($mkv,'.')>0){
    $a = explode('.',$mkv);
    $mod = $a[0];
    $kid = $a[1];
}else{
    $mod = '';
    $kid = $mkv;
} //echo "$act:$mkv; $mod:$kid<hr>";

switch($act){

// lang:cn:&recbl=redir
case 'lang':

    $recbk = req('recbk',@$_SERVER["HTTP_REFERER"]);
    $lang = $mkv;
    $flang = DIR_CODE."/lang/kvphp/core-$lang.php";
    file_exists($flang) && comCookie::oset('lang',$lang,30*86400); 
    if($recbk && !strpos($recbk,'plus/ajax/redir.php')){
        header("Location: $recbk");
    }else{
        die("::$flang:$recbk::");
    }

// /root/plus/ajax/redir.php?news.2015-a1-fhh1
// /index.php?indoc.1234-56-7890
break;
case 'defdir': 
    
    $mods = array('indoc');
    if(in_array($mod,$mods)){
        if(basEnv::isWeixin() || basEnv::isMobile()){
            $tpl = 'mob';
        }else{
            $tpl = 'umc';    
        }
    }else{
        $sdirs = vopTpls::etr1('show'); 
        $tpl = $_cbase['tpl']['tpl_dir'] = $sdirs['_defront_'];
        $hid = $sdirs['_hidden_'];
        unset($sdirs['_defront_'],$sdirs['_deadmin_'],$sdirs['_hidden_']);
        foreach($sdirs as $k=>$v){ 
            if(in_array($mod,$v)){
                $tpl = $k;
                break;
            }
        } 
    } 
    $url = surl("$tpl:$mod.$kid"); 
    if(strpos($url,'close#')) basMsg::show("$mod,$kid,$tpl<br>$url",'die');
    header("Location: $url"); 
    
// /root/plus/ajax/redir.php?advs:iaw7EyA9Qyo3q9mrqzwsSvaCEfXGEQe3KVaBiO67KvpHEt6riyXREyJH0du
break;
case 'advs':
    
    //$mkv = req('mkv');
    $mkv = explode(',',comConvert::sysBase64($mkv,'de'));
    $mod = $mkv[0];
    $aid = @$mkv[1];
    $url = @$mkv[2];
    if(empty($mod) || empty($aid) || empty($url)){
        exit("Error: [$mod,$aid,$url]");
    }else{
        db()->query("UPDATE ".$db->table("advs_$mod",2)." SET click=click+1 WHERE aid='$aid'");
        header("Location: $url");    
    }
    //check,click,dir

// /index.php?dir.yscode
break;
case 'dir':
    
    //die($mkv);
    $redir = read('vjump.redir','ex');
    if(isset($redir[$mkv])){
        header("Location: ".$redir[$mkv]);
    }//else{ die('xxx'); }
    //header("Location: $url");

break;
default: //其实无这种情况
    
    exit('Empty action!');
    
}

die();

/*
//safComm::urlFrom();
//glbHtml::head('html');

$act = req('act','chkVImg'); 
$mod = req('mod','','Key'); //basStr::filKey('');
$kid = basReq::ark('fm','kid','Key'); //echo $mod.':'.$kid;
$uid = basReq::ark('fm','uid','Key'); //echo $mod.':'.$uid;
$_groups = read('groups');

*/
