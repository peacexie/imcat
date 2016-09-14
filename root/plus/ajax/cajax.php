<?php 
require(dirname(__FILE__).'/_config.php');
//safComm::urlFrom();
glbHtml::head('html');

$db = glbDBObj::dbObj();
$act = basReq::val('act','chkVImg'); 
$mod = basReq::val('mod','','Key'); //basStr::filKey('');
$kid = basReq::ark('fm','kid','Key'); //echo $mod.':'.$kid;
$uid = basReq::ark('fm','uid','Key'); //echo $mod.':'.$uid;
$_groups = glbConfig::read('groups');

// 处理语言
$lang = isset($_GET['lang']) ? $_GET['lang'] : $_cbase['sys']['lang'];
$lang && $_cbase['sys']['lang'] = $lang;

//测试
if($act=='_istest_'){
	
}elseif($act=='fsInit'){
	
	safComm::urlStamp('check',30);
	$restr = safComm::formCInit();
	echo "document.write(\"$restr\");";
	
}elseif($act=='userExists'){ 

	if($re=basKeyid::keepCheck($uname,1,1,1)){ //$key,$chk,$fix,$grp
		die($re);
	}elseif($uinfo = $db->table("users_uacc")->where("uname='$uname'")->find()){
		die(lang('plus.cajax_userid')."[$uname](uacc)".lang('plus.cajax_exsists'));
	}elseif($uinfo = $db->table("users_$mod")->where("uname='$uname'")->find()){
		die(lang('plus.cajax_userid')."[$uname]($mod)".lang('plus.cajax_exsists'));
	}else{
	    die("success");
	}	

	
}elseif($act=='fieldExists'){ 
	
	$db = glbDBObj::dbObj();
	$sy_kids = glbConfig::read('keepid','sy');
	if($re=basKeyid::keepCheck($kid,1,1,0)){ //$key,$chk,$fix,$grp
		die($re);
	}elseif($cmod = $db->table('base_fields')->where("model='$mod' AND kid='$kid'")->find()){
		die(lang('plus.cajax_field')."[$kid]".lang('plus.cajax_exsists'));
	}elseif(isset($_groups[$kid]) && $_groups[$kid]['pid']=='types'){ //系统模型
	    die("success");
	}elseif(isset($_groups[$kid]) || strstr($sy_kids,",$kid,")){ //系统模型
	    die("[$kid]".lang('plus.cajax_sysuesed')."[mod]");
	}elseif(isset($_groups[$kid]) && $_groups[$kid]['pid']!='types'){ //系统模型
		die(lang('plus.cajax_field')."[$kid]".lang('plus.cajax_syskeep')."[k1])");
	}elseif(in_array($kid,fldCfgs::setKeeps())){
		die("[$kid]".lang('plus.cajax_mykeys'));
	}else{
	    die("success");
	}
	
}elseif($act=='fieldCatid'){ 
	
	$catid = basReq::val('catid',''); 
	$ccfg = glbConfig::read($mod,'_c'); 
	$mfields = @$ccfg[$catid]; //var_dump($mfields); 
	
	if($re=basKeyid::keepCheck($kid,1,0,1)){ //$key,$chk,$fix,$grp
		die($re);
	}elseif(!empty($mfields) && isset($mfields[$kid])){
		die(lang('plus.cajax_field')."[$kid]".lang('plus.cajax_exsists'));
	}else{
	    die("success");
	}

}elseif($act=='keyExists'){
	
	$db = glbDBObj::dbObj();
	$tab = basReq::val('tab'); 
	$_f1 = in_array($tab,array('base_catalog','base_fields','base_grade','base_menu','base_model','base_paras','types_common'));
	$_k2 = str_replace('types_','',$tab);
	$_f2 = isset($_groups[$_k2]); 
	if(!$_f1 && !$_f2) die(lang('plus.cajax_erparam'));
	$kre = strtolower(basReq::ark('fm','kre','Key')); //@$fm['kre']
	if(!$kid && $kre) $kid=$kre;
	$old = basReq::val('old_val'); 
	if($re=basKeyid::keepCheck($kid,1,0,1,($_k2?2:3))){ //$key,$chk,$fix,$grp
		die($re);
	}elseif($kid===$old){
	    die("success");
	}elseif(in_array($kid, fldCfgs::setKeeps())){
		echo "[$kid]".lang('plus.cajax_mykeys')."[mysql]";
	}elseif($flag=$db->table($tab)->where((empty($mod) ? '' : "model='$mod' AND ")."kid='$kid'")->find()){
	    echo "[$kid]".lang('plus.cajax_beuesed');
	}else{
	    die("success");
	}
	
}elseif($act=='modExists'){
	
	if($re=basKeyid::keepCheck($kid,1,1,1)){ //$key,$chk,$fix,$grp
		die($re);
	}else{
		die("success");	
	}

}elseif($act=='infoRepeat'){
	
	$fid = basReq::val('fid');
	$kwd = basReq::val('kwd'); // mod,kid(docs,user,coms,advs)
	$msg = lang('plus.cajax_erparam');
	if(!isset($_groups[$mod])) die("var _repeat_res = '$msg';");
	$mcfg = glbConfig::read($mod); 
	$para = "[$mod:$fid=$kwd]";
	if(empty($mcfg['f'][$fid])){
		$re = "$para $msg!";	
	}elseif($kwd && $tab=glbDBExt::getTable($mod)){
		$flag = $db->table($tab)->where("$fid='$kwd'")->find();
		$re = $flag ? "success" : lang('plus.cajax_repeat');
	}else{
		$re = "$para $msg!";	
	}
	echo "var _repeat_res = '$re';";

// VImg
}elseif($act=='chkVImg'){
	
	safComm::urlStamp('check');
	$mod = basReq::val('mod'); $key = basReq::val('key'); 
	$key = "{$mod}_{$key}";
	$vcode = basReq::val($key);
	//echo "$mod, $vcode";
	$re = safComm::formCVimg($mod, $vcode, 'check', 600);
	if(strstr($re,'-Error')){
	    echo lang('plus.cajax_vcerr');
	}elseif(strstr($re,'-Timeout')){
	    echo lang('plus.cajax_vctout');
	}else{
	    echo "success";
	}

}elseif($act=='cfield'){ 

	$_cfg = glbConfig::read($mod);
	$_pid = $_cfg['pid']; 
	$_tmp = array(
		'docs' =>'did',
		'users'=>'uid',
	); //'coms' =>'cid',
	if(!isset($_tmp[$_pid])) glbHtml::end(lang('plus.cajax_erparam').':mod@dop.php');
	$db = glbDBObj::dbObj();
	$data = $db->table("{$_pid}_$mod")->where("$_tmp[$_pid]='$kid'")->find(); 
	fldView::lists($mod,$data,basReq::val('catid'));

}elseif($act=='uLogin'){ 
	
	$uname = basReq::val('uname');
	$uadm = usrBase::userObj('Admin');
	if($uadm->userFlag=='Login'){
		usrBase::setLogin('m',$uname);
		header("Location:".vopUrl::fout("umc:0"));
	}else{
		echo "(uname=$uname)";	
	}
	
}else{
	
	exit('Empty action!');	
	
}


