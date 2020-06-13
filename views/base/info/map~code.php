<?php
namespace imcat;
$_cbase['skip']['_all_'] = true;
$_cbase['ucfg']['lang'] = '(auto)'; 

$urls = array(
    // baidumap,54df74a9ff8a54471a6eb1200eeff8ea,googlemap
    'baidu' => "https://api.map.baidu.com/api?v=2.0&ak={$_cbase['3aks']['baidumap']}&s=1",
    // googlemap,xxx
    'google' => "https://www.google.cn/maps/api/js?sensor=false",
);
$api = $this->view; //req('api','baidu');

$act = req('act','view');
$frmid = req('frmid','');
$title = req('title','');
$from = req('from','');

$point = req('point',''); 
$pa = explode(',',"$point,,");
$def = $_cbase['ucfg']['map'];
if(empty($pa[0])) $pa[0] = $def[0];
if(empty($pa[1])) $pa[1] = $def[1];
//$point = "$pa[0],$pa[1]"; 
$zoom = empty($pa[2]) ? $def[2] : $pa[2];
$width = 6+strlen($point)*1.5; $width<36 && $width=36;

$pshow = empty($point) ? '' : "$point".(empty($pa[2])?'12':"");
