<?php
require(dirname(__FILE__).'/_config.php'); 
glbHtml::head('html');

$act = basReq::val('act','sysinfo');
//safComm::urlFrom();

if($act=='version'){ // root/plus/api/update.php?act=version

	die($_cbase['sys']['ver']);

}elseif($act=='server'){ 
	
	echo updInfo::getServerInfo();

}elseif($act=='client'){
	
	$data = updInfo::getClientInfo();
	echo "document.write('".basJscss::jsShow($data, 0)."');";

}elseif($act=='nav'){ 

	foreach(array('version','server','client','nav',) as $key){
		echo " # <a href='?act=$key'>$key</a>";
	} echo " # ";
}
