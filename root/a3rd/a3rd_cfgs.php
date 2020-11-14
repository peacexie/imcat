<?php
//$_cbase['skip']['_all_'] = true;
define('RUN_A3RD', 1);
require_once dirname(__DIR__).'/run/_init.php'; 
require DIR_ROOT.'/cfgs/excfg/ex_a3rd.php';

if(empty($_cbase)){
    global $_cbase;
}
if($_cbase['debug']['err_mode']){
    error_reporting(E_ALL^E_WARNING^E_NOTICE); 
}else{
    error_reporting(0); 
}

$root_url = (\imcat\basEnv::isHttps() ? 'https:' : 'http:') . $_cbase['run']['roots'];
