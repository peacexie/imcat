<?php
/*
单个模板扩展函数
*/ 
class tex_user{ //extends tex_base
    
    #protected $prop1 = array();

    // 综合认证：表单,短信验证码
    static function chkAppCode($fmid,$fm,$mod){
        global $_cbase; 
        $db = db();
        $ucfg = read('user','sy');
        if($ucfg['regnow']=='sms-vcode'){ // 短信验证码
            safComm::urlFrom();
            $code = $fm['smscode']; //basReq::ark('fm','smscode');
            $mtel = str_replace(array("-","("," ",')'),'',$fm['mtel']);
            if(empty($code)) return array(lang('safcomm_vcoderr').'[0]',"$code");
            // db-check
            $stamp = $_cbase['run']['stamp']-600; 
            $rdb = $db->table('plus_smsend')->where("tel='$mtel' AND pid='sms-vcode:$code' AND atime>='$stamp' ")->find();
            if(!empty($rdb['res']) && $rdb['res']=='1:OK'){
                $db->table("plus_smsend")->data(array('pid'=>"ok-vcode:$code"))->where("kid='{$rdb['kid']}'")->update(0);
                comCookie::oset('vsms4',''); // clear.cookie
                return array(0,$code);
            }else{
                return array(lang('safcomm_vcoderr').$code,"$code");
            }
        }else{
            return safComm::formCAll($fmid);
        }
    }

    // 发激活邮件：
    static function sendActEmail($re3,$fm){
        global $_cbase; 
        $ucfg = read('user','sy'); 
        $kid = basKeyid::kidTemp();
        $enc = comConvert::sysBase64($fm['memail'].":mail-act:".$fm['uname'],0);
        $urp = "&kid=$kid&vstr=$enc&".safComm::urlStamp('init');
        $sys_name = cfg('sys_name'); 
        $re3['site'] = $sys_name;
        $re3['time'] = date('Y-m-d H:i'); 
        $re3['urlpath'] = surl('umc:0','',1)."?mkv=help-emact&mod=mail-act{$urp}"; 
        $re3['root'] = $_cbase['run']['rmain'];
        // tpl,email
        $detail = vopShow::tpl($ucfg['utpls']['mail-act'],'',$re3);
        $mail = new extEmail();
        $rem = $mail->send($fm['memail'],"$sys_name ".lang('user.uae_regactem'),$detail,$sys_name);
        // log,return
        $cfgs['kid'] = $kid;
        $cfgs['pid'] = 'mail-act:'.$fm['uname'].':'.$re3['defgrade'].':'.req('mod');
        if($rem=='SentOK'){
            $msg = lang('user.uae_sendactemok')."[SentOK]";
        }else{
            $link = "<b>".lang('user.uae_copytokefu')."</b>\n".var_export($re3,1)."";
            $msg = lang('user.uae_sendactemng')."($rem)<hr><pre>$link</pre>";
        } 
        $mail->slog(1,$cfgs);
        return $msg;
    }

    // 检测邮件激活：
    static function chkActEmail(){
        global $_cbase; 
        $db = db();
        $maxlife = 1860; // 31min(30min)
        $flag = safComm::urlStamp('flag',$maxlife);
        if($flag){
            return lang('user.uae_errtimeout');
        }
        $kid = req('kid');
        $vstr = comConvert::sysBase64(req('vstr'),1); 
        $varr = explode(':',$vstr); // 12345@qq.com:mail-act:uname
        $code = empty($varr[2]) ? '(null)' : $varr[2];
        $stamp = $_cbase['run']['stamp']-$maxlife; 
        $rdb = $db->table('plus_emsend')->where("kid='$kid' AND pid LIKE 'mail-act:%' AND atime>='$stamp'")->find();
        if(!empty($rdb['uto']) && $rdb['uto']==$varr[0]){ 
            $cfgs = explode(':',$rdb['pid']); // mail-act:uname:grade:mod
            $grade = empty($cfgs[2]) ? 'unActivated' : $cfgs[2];
            $mod = empty($cfgs[3]) ? 'person' : $cfgs[3];
            $cpid = str_replace('mail-act','ok-act',$rdb['pid']);
            usrMember::bindUser($varr[2],'memail',$varr[0]); // bind
            $db->table("plus_emsend")->data(array('pid'=>$cpid))->where("kid='$kid'")->update(0);
            $db->table("users_$mod")->data(array('grade'=>$grade))->where("uname='$cfgs[1]'")->update(0);
            return $rdb; 
        }else{
            return lang('user.uae_errvode');
        }
    }

	// 邮件找密码
    static function sendGetpw($uname, $memail){ 
        global $_cbase; 
        $uinfo = usrBase::uget_minfo($uname);
        if(!empty($uinfo['memail']) && $uinfo['memail']==$memail){
        	$enc = comConvert::sysBase64($uinfo['umods'].":mail-getpw:".$uname,0);
            $sys_name = cfg('sys_name'); 
            $ucfg = read('user','sy'); 
			$kid = basKeyid::kidTemp();
            $re3['site'] = $sys_name;
            $re3['time'] = date('Y-m-d H:i'); 
            $re3['urlpath'] = surl('umc:0','',1)."?mkv=help-getpw&emid=$kid&vstr=$enc".safComm::urlStamp('init');
            $re3['root'] = $_cbase['run']['rmain'];
            // tpl,email
            $detail = vopShow::tpl($ucfg['utpls']['mail-getpw'],'',$re3);
            $mail = new extEmail();
            $rem = $mail->send($memail,"$sys_name ".lang('user.usrm_emsubj'),$detail,$sys_name);
            $re = $rem=='SentOK' ? lang('user.usrm_emtip') : lang('user.usrm_emeror');
			// log
			if($rem=='SentOK'){
				$cfgs['kid'] = $kid;
				$cfgs['pid'] = 'mail-getpw:'.$uname.':'.$memail;
				$mail->slog(1,$cfgs);
			}
        }else{
            $re = lang('user.usrm_eremail');
        }
        return $re;
    }

    // 检测邮件找密码：
    static function chkGetpw(){
        global $_cbase; 
        $db = db();
        $maxlife = 1860; // 31min(30min)
        $flag = safComm::urlStamp('flag',$maxlife);
        if($flag){
            return lang('user.uae_errtimeout');
        }
		// 
        $kid = req('emid');
		$vstr = comConvert::sysBase64(req('vstr'),1); 
		$varr = explode(':',$vstr); // umods:mail-getpw:uname
		if(count($varr)!=3){
			return lang('user.uae_errvode');
		}
        $stamp = $_cbase['run']['stamp']-$maxlife; 
        $rdb = $db->table('plus_emsend')->where("kid='$kid' AND pid LIKE 'mail-getpw:%' AND atime>='$stamp'")->find();
        if(!empty($rdb['pid']) && strpos($rdb['pid'],":{$varr[2]}:")){ 
            $upass = basKeyid::kidRand('24',8);
            $dbpass = comConvert::sysPass($varr[2],$upass,$varr[0]); 
            $db->table("users_uacc")->data(array('upass'=>$dbpass))->where("uname='$uname'")->update(0);
			$cpid = str_replace('mail-getpw','ok-getpw',$rdb['pid']);
			$db->table("plus_emsend")->data(array('pid'=>$cpid))->where("kid='$kid'")->update(0);
			$varr['pass'] = $upass;
            return $varr; 
        }else{
            return lang('user.uae_errvode');
        }
    }


    // xxx
    static function xxx_ffff($mod,$path='',$timeout=3600){
        //
    }

}

/*

*/
