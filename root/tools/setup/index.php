<?php
require dirname(__FILE__).'/_config.php';

// Check proot
$proot = devRun::prootGet();
if($proot!=PATH_PROJ){    
    header("Location:../adbug/start.php?FixProot");    
    die();
}
// Check start
$csmsg = devRun::startCheck();    
if(!empty($csmsg)){    
    header('Location:../adbug/start.php');    
    die();    
}

$setCfgs = devSetup::supCfgs();
$func = "sup$act"; 

if($act=='EditDB'){    
    $dbname = req('dbname');    
    $dbnold = req('dbnold');
    if($dbname!==$dbnold){
        devData::rstVals(DIR_ROOT.'/cfgs/boot/cfg_db.php',array('db_name'=>$dbname),0);
    }else{
        devRun::startDbadd($dbname);
    }
    header('Location:?');
}elseif($act=='Mark'){
    @die(devSetup::$func($step));
}elseif(method_exists('devSetup',$func)){
    devSetup::$func($tab);
}

glbHtml::page(lang('tools.setup_title')." - ".$_cbase['sys_name'],1);
glbHtml::page('imp',array('css'=>'/tools/setup/style.css','js'=>'/tools/setup/sfunc.js'));
glbHtml::page('body');

$cmydb3 = devRun::runMydb3();
$cmynow = $cmydb3[glbDBObj::getCfg('db_class')];
include DIR_ROOT.'/cfgs/boot/cfg_db.php'; 

$orguser = 'adm_'.basKeyid::kidRand(0,3);
$orgpass = 'pass_'.basKeyid::kidRand(0,3);

glbHtml::ieLow_html(); 
require dirname(__FILE__).'/sflow.htm';
glbHtml::page('end');
?>