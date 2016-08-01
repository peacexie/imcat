<?php 
require(dirname(__FILE__).'/_config.php');

$db = glbDBObj::dbObj();
$qstr = $_SERVER['QUERY_STRING'];
// advs:mod.key

if(strpos($qstr,':')>0){
	$a = explode(':',$qstr);
	$act = $a[0];
	$mkv = $a[1];
}else{
	$act = 'defdir';
	$mkv = $qstr;
}
if(strpos($mkv,'.')>0){
	$a = explode('.',$mkv);
	$mod = $a[0];
	$kid = $a[1];
}else{
	$mod = '';
	$kid = $mkv;
} //echo "$act:$mkv; $mod:$kid<hr>";

// /root/plus/ajax/redir.php?news.2015-a1-fhh1
// /index.php?indoc.1234-56-7890
if($act=='defdir'){ 
	
	$mods = array('indoc');
	if(in_array($mod,$mods)){
		if(basEnv::isWeixin() || basEnv::isMobile()){
			$tpl = 'mob';
		}else{
			$tpl = 'umc';	
		}
	}else{
		$sdirs = vopTpls::etr1('show'); 
		$tpl = $_cbase['tpl']['tpl_dir'] = $sdirs['_defront_'];
		$hid = $sdirs['_hidden_'];
		unset($sdirs['_defront_'],$sdirs['_deadmin_'],$sdirs['_hidden_']);
		foreach($sdirs as $k=>$v){ 
			if(in_array($mod,$v)){
				$tpl = $k;
				break;
			}
		} 
	} 
	$url = vopUrl::fout("$tpl:$mod.$kid"); 
	if(strpos($url,'close#')) basMsg::show("$mod,$kid,$tpl<br>$url",'die');
	header("Location: $url"); 
	
// /root/plus/ajax/redir.php?advs:iaw7EyA9Qyo3q9mrqzwsSvaCEfXGEQe3KVaBiO67KvpHEt6riyXREyJH0du
}elseif($act=='advs'){
	
	//$mkv = basReq::val('mkv');
	$mkv = explode(',',comConvert::sysBase64($mkv,'de'));
	$mod = $mkv[0];
	$aid = @$mkv[1];
	$url = @$mkv[2];
	if(empty($mod) || empty($aid) || empty($url)){
		exit("Error: [$mod,$aid,$url]");
	}else{
		$db->query("UPDATE ".$db->table("advs_$mod",2)." SET click=click+1 WHERE aid='$aid'");
		header("Location: $url");	
	}
	//check,click,dir

// /index.php?dir.yscode
}elseif($act=='dir'){
	
	//die($mkv);
	$redir = glbConfig::read('redir','ex');
	if(isset($redir[$mkv])){
		header("Location: ".$redir[$mkv]);
	}//else{ die('xxx'); }
	//header("Location: $url");

}else{ //其实无这种情况
	
	exit('Empty action!');
	
}

die();

/*
//safComm::urlFrom();
//glbHtml::head('html');

$db = glbDBObj::dbObj();
$act = basReq::val('act','chkVImg'); 
$mod = basReq::val('mod','','Key'); //basStr::filKey('');
$kid = basReq::ark('fm','kid','Key'); //echo $mod.':'.$kid;
$uid = basReq::ark('fm','uid','Key'); //echo $mod.':'.$uid;
$_groups = glbConfig::read('groups');

*/
