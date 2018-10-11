<?php
//$_cbase['skip']['_all_'] = true;
define('RUN_A3RD', 1);
require dirname(__DIR__).'/run/_init.php'; 
require DIR_ROOT.'/cfgs/excfg/ex_a3rd.php';

if($_cbase['debug']['err_mode']){
    error_reporting(E_ALL^E_WARNING^E_NOTICE); 
}else{
    error_reporting(0); 
}
