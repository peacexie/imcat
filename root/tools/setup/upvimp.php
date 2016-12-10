<?php
require(dirname(__FILE__).'/_config.php');

//check, re:cfgs:
$cfg = updDbcmp::uimpCheck(); //print_r($cfg);
$nav = '';

$groups = devBase::_tabGroup();
foreach($groups as $kg){
	$igap = $kg=='exd' ? '<br>' : '#';
	$nav .= "\n $igap <a href='?act={a}&ano=$kg'>$kg</a>";
}

glbHtml::page(lang('tools.upi_title').' - '.$_cbase['sys_name'],1);
glbHtml::page('imp',array('css'=>'/tools/setup/style.css','js'=>'/tools/setup/sfunc.js'));
glbHtml::page('body');

include(vopShow::inc('/tools/setup/upvimp.htm',DIR_ROOT));

$cnew = updBase::cacGet('uimp_new');
$cold = updBase::cacGet('uimp_old');

echo "<div class='upgres'>";
if($act=='cpcfg'){
	
	updDbcmp::uimpInit();
	echo lang('tools.upi_recache');
	
}elseif($act=='cptables'){

	$ctab = updDbcmp::cmpTable($cnew,$cold);
	echo "<pre>"; print_r($ctab); echo "</pre>"; 

}elseif($act=='cpfields'){
	
	$cfields = updDbcmp::cmpField($cnew,$cold,0);
	echo "<pre>"; print_r($cfields); echo "</pre>"; 
	updBase::cacSave($cfields,'uimp_fields');
	
}elseif($act=='cpindexs'){
	
	$cindexs = updDbcmp::cmpIndex($cnew,$cold,1);
	echo "<pre>"; print_r($cindexs); echo "</pre>"; 
	updBase::cacSave($cindexs,'cimp_indexs');

}elseif($act=='sqlins' || $act=='sqlrep'){
	 
	$pr0 = in_array($ano,$groups) ? "{$ano}_" : '';
	$sqls = updDbcmp::uimpTabs($cnew,$cold,$pr0);
    $dbnc = $cfg['new'];
    $r1 = array("{pre}",            "{ext}",            "dbnew.",             "dbold.",                   "\n");
    $r2 = array($dbnc['db_prefix'], $dbnc['db_suffix'], $dbnc['db_name'].".", $cfg['old']['db_name'].".", "<br>");
	foreach($sqls as $sql){
		if($act=='sqlrep') $sql = str_replace(array("INSERT INTO "),array("REPLACE INTO "),$sql);
		$sql = str_replace($r1,$r2,$sql); 
		echo "<br><br>\n$sql";	
	}
	echo "<br><br>\n";

}
echo "</div>";

glbHtml::page('end'); 

/*
	$pr[1] = array("8:archives","5:commu","7:members",);
	$pr[2] = array("7:aalbums","7:coclass","9:farchives","4:push",); 
	$pr[3] = 'currency1,currency2,dbfields,userfiles';
	$pr[4] = 'arctemp15,housesrecords,weituos';
*/

?>
