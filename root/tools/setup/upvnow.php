<?php 
require(dirname(dirname(dirname(__FILE__))).'/run/_paths.php'); 

$act = basReq::val('act');
$step = basReq::val('step');
$flag = basReq::val('flag'); 

bootPerm_ys('pstools','','<p><a href="../adbug/binfo.php?login" target="x">login</a></p>');
$upc = updBase::preCheck(); //print_r($upc);

$msg = '请：从上到下，从左到右，点击按钮，逐步更新。';
$res = "";
if($step=='null'){
	
}elseif(in_array($step,array('code_init','root_init'))){
	$res = updAdmin::doFileInit($upc,$step);
	updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('code_add','root_add'))){
	$res = updAdmin::doFileAE($upc,$step,'fileAdd');
	$msg = "补全文件：结果列表...";
	updAdmin::setStep($upc,$step);
	$upc['steps'] .= "$step`";
}elseif(in_array($step,array('code_edit','root_edit'))){
	$res = updAdmin::doFileAE($upc,$step,'fileEdit');
	$msg = "覆盖文件：结果列表...";
	updAdmin::setStep($upc,$step);
	$upc['steps'] .= "$step`";
}elseif(in_array($step,array('code_comp','root_comp')) && empty($flag)){
	$res = updAdmin::doFileComp($upc,$step);
	$msg = "比较文件：手动处理列表...";
	$msg .= " &gt; <a href='?step=$step&flag=1'>确认[手动处理]完毕</a>";
}elseif(in_array($step,array('code_comp','root_comp')) && $flag){
	updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('code_tpls','root_skin'))){
	updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('dir_dtmp'))){
	$res = updAdmin::doDirDtmp($upc);
	$msg = "补全dtmp目录：结果列表...";
	updAdmin::setStep($upc,$step);
	$upc['steps'] .= "$step`";	
}elseif(in_array($step,array('dir_vimp3'))){	
	updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('db_init'))){	
	$res = updAdmin::doDbInit($upc); 
	updAdmin::setStep($upc,$step,1);
}elseif(in_array($step,array('db_table'))){	
	$res = updAdmin::doDbTable($upc);
	$msg = "添加数据表：结果列表...";
	updAdmin::setStep($upc,$step);
	$upc['steps'] .= "$step`";
}elseif(in_array($step,array('db_fadd'))){ // && empty($flag)
	$res = updAdmin::doDbFAdd($upc);
	$msg = "[添加]表字段：";
	updAdmin::setStep($upc,$step);
	$upc['steps'] .= "$step`";
}elseif(in_array($step,array('db_fcomp')) && empty($flag)){
	$res = updAdmin::doDbFComp($upc,$step);
	$msg = "比较字段：手动处理列表...";
	$msg .= " &gt; <a href='?step=$step&flag=1'>确认[手动处理]完毕</a>";
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
	$f = updBase::preReset(basReq::val('path','','Html'));
	header('Location:?');
}else{
	$act = 'upmain';
} 
if(empty($upc['path'])){
	$act = 'reform';
}else{
	$pp = updBase::prePsyn($upc['path']); 
}

glbHtml::page("更新程序 - 升级当前系统 - ".$_cbase['sys_name'],1);
glbHtml::page('imp');
echo basJscss::imp("/tools/setup/sfunc.js?".time());
echo basJscss::imp("/tools/setup/style.css");
glbHtml::page('body');

if(in_array($act,$carrs)){ 
	require(dirname(__FILE__).'/upcomp.htm');
}else{
	require(dirname(__FILE__).'/upflow.htm');
}

//echo "<pre>";
//print_r($upc); print_r($rep); 
glbHtml::page('end');
?>
