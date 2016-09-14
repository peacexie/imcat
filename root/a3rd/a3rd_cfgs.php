<?php
//$_cbase['skip']['_all_'] = true;
define('RUN_A3RD', 1);
require_once(dirname(dirname(__FILE__)).'/run/_paths.php'); 
require_once(DIR_CODE.'/cfgs/excfg/ex_a3rd.php');

if($_cbase['debug']['err_mode']){
	error_reporting(E_ALL^E_WARNING^E_NOTICE); 
}else{
	error_reporting(0); 
}
