<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA'); 

$stype = basReq::val('stype');
$duwhr = " AND auser='{$user->usess['uname']}'"; 

$msg = ''; $tabext = ''; 
if($view=='list'){
	
	usrPerm::issup() || usrPerm::run('pcheck',$mod);
	if(!empty($bsend)){
		require(dopFunc::modAct('list_do',$mod,$dop->type));
	} //$dop->whrstr = " AND "; $_mpid,
    if(!empty($bsend)){
        $dop->whrstr = empty($whrself) ? '' : $whrself; 
    }
	require(dopFunc::modAct('list_show',$mod,$dop->type));
	
}elseif($view=='form'){
	
    usrPerm::issup() || usrPerm::run('pcheck',$mod);
	if(!empty($bsend)){
		require(dopFunc::modAct('form_do',$mod,$dop->type));
	}else{
		if(!usrPerm::issup()){
        foreach($dop->cfg['i'] as $k=>$v){
            if(!in_array($stype,array($k,$v['pid']))) { unset($dop->cfg['i'][$k]); }
		}} 
        require(dopFunc::modAct('form_show',$mod,$dop->type));
	}
    if(!usrPerm::issup()){
        echo basJscss::jscode("setEdit('click,xmpic');"); // ������
    }else{
        echo basJscss::jscode("setEdit('auser,euser','click,xmpic');"); // add,edit=�Լ���
    }
	
}elseif(in_array($view,array('vself','vsite'))){

	$stype = 'afauser'; $actstr = ''; $whrself = $duwhr; 
	include dirname(__DIR__)."/eact/adfavor_view.php";
    $stype = 'afadmin'; $actstr = ''; $whrself = '';
    include dirname(__DIR__)."/eact/adfavor_view.php";

}
