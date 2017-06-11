<?php
if(!isset($_cbase['skip'])) $_cbase['skip']['_all_'] = true;
$_cbase['ucfg']['lang'] = '(auto)'; 
require dirname(dirname(dirname(__FILE__))).'/run/_init.php'; 

$act = req('act','view');
$frmid = req('frmid','');
$point = req('point','');
$title = req('title','');
