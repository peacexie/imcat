<?php
if(!isset($_cbase['skip'])) $_cbase['skip']['_all_'] = true;
$_cbase['ucfg']['lang'] = '(auto)'; 
require(dirname(dirname(dirname(__FILE__))).'/run/_paths.php'); 

$act = basReq::val('act','view');
$frmid = basReq::val('frmid','');
$point = basReq::val('point','');
$title = basReq::val('title','');
