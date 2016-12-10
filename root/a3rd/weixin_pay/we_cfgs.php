<?php
//define('RUN_AJAX', 1);  
//if(!isset($_cbase['skip'])) $_cbase['skip']['_all_'] = true;
require(dirname(dirname(dirname(dirname(__FILE__)))).'/root/run/_init.php'); 

if(empty($_cbase['run']['wedemo'])){
	//usrPerm::run('pmod','apiweixin');
}
extract(basReq::sysVars());

function wxDebugNavbar(){
	$s = "\n<p class='tc'>";
	$s .= "Nav : \n<a href='wedebug.php'>wedebug</a>";
	$s .= " # \n<a href='wedemo.php'>wedemo</a>";
	$s .= " # \n<a href='wejsdk.php'>wejsdk</a>";
	$s .= " # \n<a href='wetest.php'>wetest</a>";
	$s .= "</p><hr>\n";
	echo $s;
}

/*
- 素材管理, wex_material
- 微网 wex_web
- 微店 wex_shop
- 微活动 (签到，场景) wex_acts
*/
