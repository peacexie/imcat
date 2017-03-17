<?php    
require(dirname(__FILE__).'/_config.php');    

bootPerm_ys('pstools','','<p><a href="../adbug/binfo.php?login" target="x">login</a></p>');
$upc = updBase::preCheck(); 

$msg = lang('tools.upn_tip1'); 
$res = "";
if($step=='null'){
    
}elseif(in_array($step,array('code_init','root_init'))){
    $res = updAdmin::doFileInit($upc,$step);
    updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('code_add','root_add'))){
    $res = updAdmin::doFileAE($upc,$step,'fileAdd');
    $msg = lang('tools.upn_afres');
    updAdmin::setStep($upc,$step);
    $upc['steps'] .= "$step`";
}elseif(in_array($step,array('code_edit','root_edit'))){
    $res = updAdmin::doFileAE($upc,$step,'fileEdit');
    $msg = lang('tools.upn_ofres');
    updAdmin::setStep($upc,$step);
    $upc['steps'] .= "$step`";
}elseif(in_array($step,array('code_comp','root_comp')) && empty($flag)){
    $res = updAdmin::doFileComp($upc,$step);
    $msg = lang('tools.upn_cpres');
    $msg .= " &gt; <a href='?step=$step&flag=1'>".lang('tools.upn_cnfm')."</a>";
}elseif(in_array($step,array('code_comp','root_comp')) && $flag){
    updAdmin::setStep($upc,$step,1);
//}elseif(in_array($step,array('code_tpls','root_skin'))){
    //updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('dir_dtmp'))){
    $res = updAdmin::doDirDtmp($upc);
    $msg = lang('tools.upn_dtmp');
    updAdmin::setStep($upc,$step);
    $upc['steps'] .= "$step`";    
}elseif(in_array($step,array('dir_vimp3'))){    
    updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('db_init'))){    
    $res = updAdmin::doDbInit($upc);    
    updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('db_table'))){    
    $res = updAdmin::doDbTable($upc);
    $msg = lang('tools.upn_addtb');
    updAdmin::setStep($upc,$step);
    $upc['steps'] .= "$step`";
}elseif(in_array($step,array('db_fadd'))){ // && empty($flag)
    $res = updAdmin::doDbFAdd($upc);
    $msg = lang('tools.upn_addfld');
    updAdmin::setStep($upc,$step);
    $upc['steps'] .= "$step`";
}elseif(in_array($step,array('db_fcomp')) && empty($flag)){
    $res = updAdmin::doDbFComp($upc,$step);
    $msg = lang('tools.upn_cmpfld');
    $msg .= " &gt; <a href='?step=$step&flag=1'>".lang('tools.upn_cnfm')."</a>";
}elseif(in_array($step,array('db_fcomp')) && $flag){
    updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('db_data'))){
    updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('end_ver'))){
    updAdmin::doVerset($upc);
    updAdmin::setStep($upc,$step,1);    
}elseif(in_array($step,array('end_flag'))){
    $upc['done'] = 'locked';
    updAdmin::setStep($upc,$step,1);
}    

$carrs = array('cmpfile','cmptable','cmpdata');
if(in_array($act,$carrs)){    
    //
}elseif($act=='reset'){    
    $f = updBase::preReset(req('path','','Html'));
    header('Location:?');
}else{
    $act = 'upmain';
}    
if(empty($upc['path'])){
    $act = 'reform';
}else{
    $pp = updBase::prePsyn($upc['path']);    
}    //    $act = 'upmain';

glbHtml::page(lang('tools.upn_title').' - '.$_cbase['sys_name'],1);
glbHtml::page('imp',array('css'=>'/tools/setup/style.css','js'=>'/tools/setup/sfunc.js'));
glbHtml::page('body');

if(in_array($act,$carrs)){    
    include(vopShow::inc('/tools/setup/upcomp.htm',DIR_ROOT));
}else{
    include(vopShow::inc('/tools/setup/upflow.htm',DIR_ROOT));
}

glbHtml::page('end');
?>
