<?php
$_cbase['skip']['_all_'] = true;
require(dirname(dirname(dirname(__FILE__))).'/run/_paths.php'); 

$urls = array(
	'baidu' => 'http://api.map.baidu.com/api?v=1.4',
	'google' => 'http://www.google.cn/maps/api/js?sensor=false',
);
$api = basReq::val('api','baidu');

$act = basReq::val('act','view');
$frmid = basReq::val('frmid','');
$title = basReq::val('title','');

$point = basReq::val('point',''); 
$pa = explode(',',"$point,,");
if(empty($pa[0])) $pa[0] = 116.4040;
if(empty($pa[1])) $pa[1] = 39.9151;
//$point = "$pa[0],$pa[1]"; 
$zoom = empty($pa[2]) ? 12 : $pa[2];
$width = 6+strlen($point)*1.5; $width<36 && $width=36;

