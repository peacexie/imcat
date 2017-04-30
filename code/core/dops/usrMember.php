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
            $re['erno'] = "model:$mod:Error!";
            $re['ermsg'] = "model[$mod]Error!";
            return $re;
        }
        $uname = self::addUname($uname,$mod);
        $uarr = self::addUid(@$excfg['uid']); $uid = $uarr['uid']; $uno = $uarr['uno']; 
        @$mcfg = basElm::text2arr($md['cfgs']); 
        $defgrade = (isset($mcfg['defgrade']) && isset($md['i'][$mcfg['defgrade']])) ? $mcfg['defgrade'] : '';
        $grade = isset($excfg['grade']) ? $excfg['grade'] : $defgrade; 
        @$defshow = in_array($mcfg['defcheck'],array('Y','1','y')) ? '1' : 0;
        @$show = intval($excfg['check']) ? intval($excfg['check']) : $defshow; 
        $mname = $mname ? $mname : $uname; 
        $mtel = $mtel ? $mtel : '127-6666-8888'; 
        $memail = $memail ? $memail : "$mname@domain.com";
        $upass = comConvert::sysPass($uname,$upass,$mod);
        $acc = array('uid'=>$uid,'uno'=>$uno,'uname'=>$uname,'upass'=>$upass,'umods'=>$mod,); 
        $dataex = basSql::logData();
        $db->table('users_uacc')->data($acc+$dataex)->insert(); 
        $umd = array('uid'=>$uid,'uname'=>$uname,'grade'=>$grade,'mname'=>$mname,'mtel'=>$mtel,'memail'=>$memail,'show'=>$show,);
        if(isset($md['f']['company']) && isset($excfg['company'])) $umd['company'] = $excfg['company']; 
        $db->table("users_$mod")->data($umd+$dataex)->insert();
        $re = array('uid'=>$uid,'grade'=>$grade,'check'=>$show,'uname'=>$uname,'defgrade'=>$defgrade,);
        comJifen::main(array_merge($md,array('uid'=>$uid,'auser'=>$uname,'defgrade'=>$defgrade)),'add','User-Reg');
        return $re;
    }
    
    static function addUname($uname='',$mod=''){ 
        $tabid = 'users_uacc'; $key = "uname";
        if(empty($uname)){
            $uname = substr($mod,0,1).str_replace('-','',basKeyid::kidTemp('5'));
        }
        $r = db()->table($tabid)->field($key)->where("$key='$uname'")->find(); 
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
        $r = db()->table($tabid)->field($key)->where("$key='$uid'")->find(); 
        if(!empty($r[$key])){ 
            return self::addUid();
        }
        return array('uid'=>$uid,'uno'=>$uno);
    }
    
    static function bindUser($mname,$pptmod,$pptuid){ 
        db()->table('users_uppt')->data(array('uname'=>$mname, 'pptmod'=>$pptmod, 'pptuid'=>$pptuid))->insert();
    }

    static function chkExists($key,$val,$mod=''){ 
        $db = db();
        $_groups = read('groups');    
        if($key=='uname' && $re=basKeyid::keepCheck($val,1,1,1)){
            return $re;
        }
        if($key=='uname'){
            if($uinfo = $db->table("users_uacc")->where("uname='$val'")->find()){
                return lang('plus.cajax_userid')."[$val](uacc)".lang('plus.cajax_exsists');
            }
            if($mod && isset($_groups[$mod]) && $_groups[$mod]['pid']=='users'){
                if($uinfo = $db->table("users_$mod")->where("uname='$val'")->find()){
                    return lang('plus.cajax_userid')."[$val]($mod)".lang('plus.cajax_exsists');
                }
            }
        }elseif($key=='memail' || $key=='mtel'){
            if($key=='memail' && !basStr::isMail($val)){
                $tmsg = lang('plus.cajax_mailid');
                return "Error $tmsg:[$val]!";
            };
            if($key=='mtel' && !basStr::isTel($val)){
                $tmsg = lang('plus.cajax_telnum');
                return "Error $tmsg:[$val]!";
            };
            if($uinfo = $db->table("users_uppt")->where("pptmod='$key' AND pptuid='$val'")->find()){
                return $tmsg."[$val](uacc)".lang('plus.cajax_exsists');
            }
        }
        return "success";
    }

}
