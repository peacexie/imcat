<?php
require(dirname(__FILE__).'/_config.php'); 
glbHtml::head('html');

$act = req('act','sysinfo');
//safComm::urlFrom();

if($act=='version'){ // root/plus/api/update.php?act=version

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
    #glbHtml::page('imp');
    #basMsg::show($data,'Redir',"?file=admin/upgrade&mod=install");
    dump($data);

}elseif($act=='nav'){ 

    $nav = array('version','server','client','table','fatch','nav',);
    exvFunc::navShow($nav,'act');

}
