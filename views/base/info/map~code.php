<?php
namespace imcat;
$_cbase['skip']['_all_'] = true;
$_cbase['ucfg']['lang'] = '(auto)'; 

$urls = array(
    'baidu' => 'http://api.map.baidu.com/api?v=1.4',
    'google' => 'http://www.google.cn/maps/api/js?sensor=false',
);
$api = $this->view; //req('api','baidu');

$act = req('act','view');
$frmid = req('frmid','');
$title = req('title','');
$from = req('from','');

$point = req('point',''); 
$pa = explode(',',"$point,,");
if(empty($pa[0])) $pa[0] = 116.4040;
if(empty($pa[1])) $pa[1] = 39.9151;
//$point = "$pa[0],$pa[1]"; 
$zoom = empty($pa[2]) ? '12' : $pa[2];
$width = 6+strlen($point)*1.5; $width<36 && $width=36;

$pshow = empty($point) ? '' : "$point,$zoom";
