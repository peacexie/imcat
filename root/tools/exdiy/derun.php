<?php 
$_cbase['skip']['_paths'] = true;
$_fpcfg = dirname(__FILE__).'/_config.php';
if(file_exists($_fpcfg)) require $_fpcfg; 

require DIR_STATIC.$_locfp;
