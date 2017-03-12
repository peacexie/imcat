<?php
require(dirname(__FILE__).'/_config.php'); 
glbHtml::head('html');

$nav[1] = array('nav','mscfg','msuply','mfatch'); 
$nav[2] = array('version','server','client','table','fatch',);

$act = req('act','sysinfo');
//safComm::urlFrom();

/* 权限检查
 - 站群同步/云更新/授权更新
 - [授权更新] db: domain,sn
 - url: domain,stamp,enc; enc=md5("$sn.$domain.$stamp")
 - [站群同步] cfg: domain-table (所有sn一致)
 - url: sub-domain,stamp,enc; enc=md5("$sn.$domain.$stamp")
*/
if(in_array($act,array('mscfg','msuply','mfatch'))){
    die('perm...');
}

if($act=='nav'){ 
    foreach (array(1,2) as $no) {
        exvFunc::navShow($nav[$no],'act');
    }
// multi/cloude/license
}elseif($act=='mscfg'){ 
    $data = read('vjump.sites','ex'); 
    echo comParse::jsonEncode($data);
}elseif($act=='msuply'){ 
    $dir = req('dir');
    $fp = req('fp'); die('msuply');
    // check-dir, check-fp
    // 保留哪些文件...
    $dir = comStore::cfgDirPath($dir);
    $data = comFiles::get(str_replace(array('//',"\\"),'/',"$dir/$fp"));
    basEnv::obClean();
    die($data);
}elseif($act=='mfatch'){ 
    $dir = req('dir');
    $fp = req('fp'); die('mfatch');
    // check-dir, check-fp
    $svr = req('svr'); // 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']
    $data = $svr ? file_get_contents("$svr?act=msuply&dir=$dir&fp=$fp") : ''; 
    if($data){
        $dir = comStore::cfgDirPath($dir);
        $f = comFiles::put(str_replace(array('//',"\\"),'/',"$dir/$fp".'1'),$data);
        die('OK!');
    }else{
        die('Error!');
    }
// single
}elseif($act=='version'){ // root/plus/api/update.php?act=version
    die($_cbase['sys']['ver']);
}elseif($act=='server'){ 
    echo updInfo::getServerInfo();
}elseif($act=='client'){
    $data = updInfo::getClientInfo();
    echo "document.write('".basJscss::jsShow($data, 0)."');";
}elseif($act=='table'){ //mins-server
    $data = updInfo::minsTable();
    echo comParse::jsonEncode($data);
}elseif($act=='down'){ //mins-server
    $aud = req('aud');
    $kid = req('kid'); //*.dbins/php/html/htm
    $fp = DIR_DTMP."/updsvr/ins~$kid";
    if(empty($aud)){
        comHttp::downLoad($fp);
    }else{
        echo comFiles::get($fp);
    }
}elseif($act=='fatch'){ //mins-client
    $data = updInfo::minsFatch(); 
    $data = empty($data) ? 'Null:Update' : $data;
    dump($data);
}
