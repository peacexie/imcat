<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$_groups = read('groups'); 
$ucfg = read('user','sy'); 
$mod = req('mod');

$show = "<a href='?reg'>".lang('user.uap_reapp')."</a>";
if($act=='doapply'){
    $bsend = 1;
    $fm = in($_POST['fm'],'Title'); 
    // - mob; // mname; mtel|memail; 
    $ismobflag = req('ismobflag');
    if($ismobflag){
        if($ucfg['regnow']=='sms-vcode'){
            if(empty($fm['mname'])) $fm['mname'] = $fm['mtel']; 
        }elseif($ucfg['regnow']=='mail-act'){
            if(empty($fm['mname'])) $fm['mname'] = $fm['memail']; 
        }else{
            if(empty($fm['mname'])) $fm['mname'] = $fm['uname']; 
        }
        if(empty($fm['mtel'])) $fm['mtel'] = '126-8888-8888'; 
        if(empty($fm['memail'])) $fm['memail'] = $fm['mtel'].$_cbase['run']['dmtop']; 
    }
    $re2 = \imcat\umc\texUser::chkAppCode('fmapply',$fm,$mod); // safComm::formCAll
    if(empty($re2[0])){ 
        $arr = array('company'=>@$fm['company'],); 
        if(in_array($ucfg['regnow'],array('mail-act'))){
            $arr['grade'] = 'unActivated'; 
        }
        $re3 = usrMember::addUser($mod,$fm['uname'],$fm['upass'],$fm['mname'],$fm['mtel'],$fm['memail'],$arr);
        if(empty($re3['erno'])){
            if($ucfg['regnow']=='mail-act'){
                $msg_em = \imcat\umc\texUser::sendActEmail($re3,$fm);
                //$msg_em = "<br>邮件激活…";
            }elseif($ucfg['regnow']=='sms-vcode'){
                usrMember::bindUser($fm['uname'],'mtel',$fm['mtel']); // bind
            }
            $fappok = 1;
            $msg = "[".$re3['uname']."] ".lang('user.uap_appok');
            $show = empty($re3['show']) ? '('.lang('user.uap_nochk').')' : lang('user.uap_chkok');
        }else{
            $msg = "[".$re3['erno']."] ".$re3['ermsg'];
        }
    }else{
        $msg = "[".$re2[1]."]".$re2[0];
    }
}
$phem = $phsms = '';
