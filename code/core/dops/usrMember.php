<?php
(!defined('RUN_INIT')) && die('No Init');

// usrMember
class usrMember extends usrBase{
	
	//public $sessid = '';
	
	function __construct() {
		parent::__construct('member'); 
	}

	//
	function login($uname='',$upass='',$ck=0){
		$re1 = $this->check_login($uname,$upass); 
		$re2 = $this->login_msg($re1);
		if($re1=='OK'){ //Session
			//$this->setSess();
		}
		return array($re1,$re2);
	}
	
	// 
	function logout(){ 
		$re1 = $this->check_logout();
		if($re1!='Forbid'){ 
			comSession::set($this->sessid,''); 
		}else{
			//echo "$re1";	
		}
		return $re1;
	}
	
	// mod,uname,upass; mname,mtel,memail; company,uid,grade,check
	static function addUser($mod,$uname,$upass,$mname='',$mtel='',$memail='',$excfg=array()){ 
		$arr = array('uname','mname','mtel','memail'); foreach($arr as $k){ $$k = basStr::filTitle($$k); }
		if(isset($excfg['company'])) { $excfg['company'] = basStr::filTitle($excfg['company']); }
		$db = db(); 
		$re = array('erno'=>'','ermsg'=>'');
		$md = read($mod);
		if($md['pid']!='users'){
			$re['erno'] = "model:$mod:Error£¡";
			$re['ermsg'] = "model[$mod]´íÎó£¡";
			return $re;
		}
		$uname = self::addUname($uname,$mod);
		$uarr = self::addUid(@$excfg['uid']); $uid = $uarr['uid']; $uno = $uarr['uno']; 
		@$mcfg = basElm::text2arr($md['cfgs']); 
		$defgrade = (isset($mcfg['defgrade']) && isset($md['i'][$mcfg['defgrade']])) ? $mcfg['defgrade'] : '';
		$grade = isset($excfg['grade']) ? $excfg['grade'] : $defgrade; 
		@$defshow = in_array($mcfg['defcheck'],array('Y','1')) ? '1' : 0;
		@$show = intval($excfg['check']) ? intval($excfg['check']) : $defshow; 
		$mname = $mname ? $mname : $uname; 
		$mtel = $mtel ? $mtel : '127-6666-8888'; 
		$memail = $memail ? $memail : "$mname@domain.com";
		$tabid = $md['pid']."_$mod";
		$upass = comConvert::sysPass($uname,$upass,$mod);
		$acc = array('uid'=>$uid,'uno'=>$uno,'uname'=>$uname,'upass'=>$upass,'umods'=>$mod,); 
		$dataex = basSql::logData();
		$db->table('users_uacc')->data($acc+$dataex)->insert(); 
		$umd = array('uid'=>$uid,'uname'=>$uname,'grade'=>$grade,'mname'=>$mname,'mtel'=>$mtel,'memail'=>$memail,'show'=>$show,);
		if(isset($md['f']['company']) && isset($excfg['company'])) $umd['company'] = $excfg['company']; 
		$db->table("users_$mod")->data($umd+$dataex)->insert();
		$re = array('uid'=>$uid,'grade'=>$grade,'check'=>$show,'uname'=>$uname,);
		comJifen::main(array_merge($md,array('uid'=>$uid,'auser'=>$uname)),'add','×¢²á¼Ó·Ö');
		return $re;
	}
	
	static function addUname($uname='',$mod=''){ 
		$tabid = 'users_uacc'; $key = "uname";
		if(empty($uname)){
			$uname = substr($mod,0,1).str_replace('-','',basKeyid::kidTemp('5'));
		}
		$r = db()->table($tabid)->field($key)->where("$key='$uname'")->find(); //print_r($r);
		if(!empty($r[$key])){ 
			return self::addUname('',$mod);
		}
		return $uname;
	}
	
	static function addUid($uid=''){ 
		$tabid = 'users_uacc'; $key = "uid";
		if(empty($uid)){
			$kar = glbDBExt::dbAutID($tabid,'yyyy-md-','31');
			$uid = $kar[0]; $uno = $kar[1];	
		}else{
			$uno = '1';	
		}
		$r = db()->table($tabid)->field($key)->where("$key='$uid'")->find(); //print_r($r);
		if(!empty($r[$key])){ 
			return self::addUid();
		}
		return array('uid'=>$uid,'uno'=>$uno);
	}
	
	static function bindUser($mname,$pptmod,$pptuid){ 
		db()->table('users_uppt')->data(array('uname'=>$mname, 'pptmod'=>$pptmod, 'pptuid'=>$pptuid))->insert();
	}
	
	static function emailGetpw($uname, $memail){ 
		$uinfo = self::uget_minfo($uname);
		if(!empty($uinfo['memail']) && $uinfo['memail']==$memail){
			$upass = basKeyid::kidRand('24',8);
			$dbpass = comConvert::sysPass($uname,$upass,$uinfo['umods']); 
			db()->table("users_uacc")->data(array('upass'=>$dbpass))->where("uname='$uname'")->update();
			$upass = comConvert::sysRevert($upass, 0, '', 3600);
			$url = surl('umc:0','',1)."?mkv=user-getpw&act=emshow&upass=$upass";
			$sys_name = cfg('sys_name'); 
			$mail = new extEmail();
			$subj = lang('usrm_emsubj');
			$data = basLang::inc('uless','userm_empw',array('uname'=>$uname,'sys_name'=>$sys_name,'url'=>$url));
			$re = $mail->send($memail,$subj,$data,$sys_name);
			$re = $re=='SentOK' ? lang('usrm_emtip') : lang('usrm_emeror');
		}else{
			$re = lang('usrm_eremail');
		}
		return $re;
	}	

}
