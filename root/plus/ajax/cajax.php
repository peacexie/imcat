<?php 
require dirname(__FILE__).'/_config.php';
//safComm::urlFrom();
glbHtml::head('html');

$db = db();
$act = req('act','chkVImg'); 
$mod = req('mod','','Key'); //basStr::filKey('');
$kid = basReq::ark('fm','kid','Key'); 
$uid = basReq::ark('fm','uid','Key'); 
$_groups = read('groups');

// 处理语言
$lang = req('lang',$_cbase['sys']['lang']);
$lang && $_cbase['sys']['lang'] = $lang;

switch($act){

//测试
case '_istest_':

    //test

break;
case 'fsInit':
    
    safComm::urlStamp('check',30);
    $restr = safComm::formCInit();
    echo "document.write(\"$restr\");";
    
break;
case 'userExists':

    $key = req('key','uname');
    $val = req($key); $val || $val = basReq::ark('fm',$key);
    $res = usrMember::chkExists($key,$val,$mod);
    die($res);

break;
case 'fieldExists':
    
    $sy_kids = read('keepid','sy');
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
    
break;
case 'fieldCatid':
    
    $catid = req('catid',''); 
    $ccfg = read($mod,'_c'); 
    $mfields = @$ccfg[$catid]; 
    
    if($re=basKeyid::keepCheck($kid,1,0,1)){ //$key,$chk,$fix,$grp
        die($re);
    }elseif(!empty($mfields) && isset($mfields[$kid])){
        die(lang('plus.cajax_field')."[$kid]".lang('plus.cajax_exsists'));
    }else{
        die("success");
    }

break;
case 'keyExists':
    
    $tab = req('tab'); 
    $_f1 = in_array($tab,array('base_catalog','base_fields','base_grade','base_menu','base_model','base_paras','types_common'));
    $_k2 = str_replace('types_','',$tab);
    $_f2 = isset($_groups[$_k2]); 
    if(!$_f1 && !$_f2) die(lang('plus.cajax_erparam'));
    $kre = strtolower(basReq::ark('fm','kre','Key')); //@$fm['kre']
    if(!$kid && $kre) $kid=$kre;
    $old = req('old_val'); 
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
    
break;
case 'modExists':
    
    if($re=basKeyid::keepCheck($kid,1,1,1)){ //$key,$chk,$fix,$grp
        die($re);
    }else{
        die("success");    
    }

break;
case 'infoRepeat':
    
    $fid = req('fid');
    $kwd = req('kwd'); // mod,kid(docs,user,coms,advs)
    $msg = lang('plus.cajax_erparam');
    if(!isset($_groups[$mod])) die("var _repeat_res = '$msg';");
    $mcfg = read($mod); 
    $para = "[$mod:$fid=$kwd]";
    if(empty($mcfg['f'][$fid])){
        $re = "$para $msg!";    
    }elseif($kwd && $tab=glbDBExt::getTable($mod)){
        $flag = $db->table($tab)->where("$fid='$kwd'")->find();
        $re = $flag ? lang('plus.cajax_repeat') : "success";
    }else{
        $re = "$para $msg!";    
    }
    echo "var _repeat_res = '$re';";

// VImg
break;
case 'chkVImg':
    
    safComm::urlStamp('check');
    $mod = req('mod'); $key = req('key'); 
    $key = "{$mod}_{$key}";
    $vcode = req($key);
    $re = safComm::formCVimg($mod, $vcode, 'check', 600);
    if(strstr($re,'-Error')){
        echo lang('plus.cajax_vcerr');
    }elseif(strstr($re,'-Timeout')){
        echo lang('plus.cajax_vctout');
    }else{
        echo "success";
    }

break;
case 'cfield':

    $_cfg = read($mod);
    $_pid = $_cfg['pid']; 
    $_tmp = array(
        'docs' =>'did',
        'users'=>'uid',
    ); //'coms' =>'cid',
    if(!isset($_tmp[$_pid])) glbHtml::end(lang('plus.cajax_erparam').':mod@dop.php');
    $data = $db->table("{$_pid}_$mod")->where("$_tmp[$_pid]='$kid'")->find(); 
    fldView::lists($mod,$data,req('catid'));

break;
case 'uLogin':
    
    $uname = req('uname');
    $uadm = user('Admin');
    if($uadm->userFlag=='Login'){
        usrBase::setLogin('m',$uname);
        header("Location:".surl("umc:0"));
    }else{
        echo "(uname=$uname)";    
    }

break;
case 'chku_exist':
    
break;
default:
    
    exit('Empty action!');    
    
}//end switch

/*
$rdb = $db->table('plus_emsend')->where("kid='$kid' AND pid='mail-act:$code'")->find();
*/
