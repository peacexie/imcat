<?php
if(!isset($_cbase['skip'])) $_cbase['skip']['_all_'] = true;
$_cbase['ucfg']['lang'] = '(auto)'; 
$_cbase['tpl']['vdir'] = 'adm';
require dirname(dirname(__DIR__)).'/run/_init.php'; 

$act = req('act','view');
$frmid = req('frmid','');
$point = req('point','');
$title = req('title','');
