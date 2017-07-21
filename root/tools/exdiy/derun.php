<?php 
$_cbase['skip']['_paths'] = true;
$_fpcfg = dirname(__FILE__).'/_config.php';
if(file_exists($_fpcfg)) require $_fpcfg; 

$_locfp = '/ximp/files/derun.imp_php';
require DIR_STATIC.$_locfp;
