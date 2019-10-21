<?php
namespace imcat;
$_cbase['ucfg']['lang'] = '(auto)'; // 切换语言
include dirname(dirname(__DIR__)).'/run/_init.php';

$sid = usrPerm::getSessid(); 
$sval = empty($_SESSION[$sid]) ? '' : $_SESSION[$sid]; 
$v403 = !basEnv::isLocal() && !$sval; //dump("$sid-$sval");

// idea From : Symfony : config.php
if(IS_CLI){
    die('This script cannot be run from the CLI. <br>Please run it from a browser.');
}
if($v403){
    $msg = lang('tools.exdiy_403');
    glbHtml::httpStatus(403); 
    glbHtml::end($msg);
}
