<?php // dops
(!defined('RUN_MODE')) && die('No Init');
$mod = empty($mod) ? 'demo' : $mod;
$view = empty($view) ? 'list' : $view;
$_cfg = glbConfig::read($mod); 

$_pid = @$_cfg['pid']; 
$_tmp = array(
	'docs' =>array('dopDocs','did'),
	'users'=>array('dopUser','uid'),
	'coms' =>array('dopComs','cid'),
	'advs' =>array('dopAdvs','aid'),
); 
if(!isset($_tmp[$_pid])) glbHtml::end(lang('flow.dops_parerr').':mod@dop.php');
$gname = $_groups[$mod]['title']; //print_r($cv);
usrPerm::run('pmod',$mod);

$_cls = $_tmp[$_pid][0]; 
$dop = new $_cls($_cfg); 
$so = $dop->so; 
$cv = $dop->cv;
unset($_cfg,$_pid,$_tmp,$_cls);

$_scdir = dirname(dirname(__FILE__)); // 脚本: 
if($_fex=dopFunc::modFile($_scdir,$mod,$dop->type)){
	require($_fex); 
}else{
	$msg = ''; $tabext = '';
	if($view=='list'){
		if(!empty($bsend)){
			require(dopFunc::modAct($_scdir,'list_do',$mod,$dop->type));
		} //$dop->whrstr = " AND "; $_mpid,
		require(dopFunc::modAct($_scdir,'list_show',$mod,$dop->type));
	}elseif($view=='form'){
		if(!empty($bsend)){
			require(dopFunc::modAct($_scdir,'form_do',$mod,$dop->type));
		}else{
			require(dopFunc::modAct($_scdir,'form_show',$mod,$dop->type));
		}
	}
}

//print_r(basDebug::runInfo());
//print_r(basDebug::bugStop()); 
