<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

// usrMember
class usrMember extends usrBase{
    
    //public $sessid = '';
    
    function __construct() {
        parent::__construct('member'); 
    }

    //
    function login($uname='',$upass='',$ck=0){
        $re1 = $this->check_login($uname,$upass); //dump($re1); 
        //$re1 = $this->remote($uname,$upass,$re1);
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
    
    function remote($uname,$upass,$re1){
        $db = glbDBObj::dbObj();
        //$_f = $re1=='noChecked' || is_numeric($re1);
        if($re1!='noChecked') return $re1; // 非账号密码出错
        $ubase = $db->table('users_uacc')->where("uname='$uname'")->find(); 
        if(!empty($ubase)){
            return $re1;
        }
        $epw = MD5_Mem("$upass$uname");
        $epw = substr($epw,0,16).strrev(substr($epw,16));
        #Left(MemPW,16)&StrReverse(Mid(MemPW,17))
        $url = "http://www.xxx.com/member/mcapi.asp?MemID=$uname&MemPW=$epw";
        $data = comHttp::doGet($url);
        $data = str_replace("'", '"', $data);
        $data = json_decode($data,1); //dump($data);
        if(count($data)<5) return $re1;
        $re = self::addUser('person',$uname,$upass,$data['MemName'],$data['MemMobile'],$data['MemEmail']);
        if(empty($re['uid'])) return $re1;
        $tmp = explode('^', $data['MemFrom']); //dump($tmp);
        $detail = array(
            'mphone'=>$data['MemMobile'], 'mtel'=>$data['MemTel'], 'memail'=>$data['MemEmail'],
            'maddr'=>$tmp[2], 'maddr2'=>$tmp[3], 'mcity'=>$tmp[4], 
            'mprovince'=>$tmp[5], 'mpcode'=>$tmp[6], 'mstate'=>$tmp[8], 
            'mtitle'=>($data['MemMobile']=='F'?'Miss':'Mr'), 
        );
        $db->table('users_person')->data($detail)->where("uid='{$re['uid']}'")->update();
        return $this->check_login($uname,$upass);
    }

    // mod,uname,upass; mname,mtel,memail; company,uid,grade,check
    static function addUser($mod,$uname,$upass,$mname='',$mtel='',$memail='',$excfg=array()){ 
        $arr = array('uname','mname','mtel','memail'); foreach($arr as $k){ $$k = basStr::filTitle($$k); }
        if(isset($excfg['company'])) { $excfg['company'] = basStr::filTitle($excfg['company']); }
        $db = glbDBObj::dbObj(); 
        $re = array('erno'=>'','ermsg'=>'');
        $md = glbConfig::read($mod);
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
        $mtel = $mtel ? $mtel : '132-6666-8888'; 
        $memail = $memail ? $memail : "$mname@domain.com";
        $upass = comConvert::sysPass($uname,$upass,$mod);
        $acc = array('uid'=>$uid,'uno'=>$uno,'uname'=>$uname,'upass'=>$upass,'umods'=>$mod,); 
        $dataex = basSql::logData();
        $db->table('users_uacc')->data($acc+$dataex)->insert(); 
        $umd = array('uid'=>$uid,'uname'=>$uname,'grade'=>$grade,'mname'=>$mname,'mtel'=>$mtel,'memail'=>$memail,'show'=>$show,);
        if(isset($md['f']['company']) && isset($excfg['company'])) $umd['company'] = $excfg['company']; 
        if(isset($md['f']['mstate']) && isset($excfg['mstate'])) $umd['mstate'] = $excfg['mstate']; 
        $db->table("users_$mod")->data($umd+$dataex)->insert();
        $re = array('uid'=>$uid,'grade'=>$grade,'check'=>$show,'uname'=>$uname,'defgrade'=>$defgrade,);
        comJifen::main(array_merge($md,array('uid'=>$uid,'auser'=>$uname,'defgrade'=>$defgrade)),'add','User-Reg');
        return $re;
    }
    // wechat(28)o9PAcuAerrObVtcXgKzXllG31twM, wework(64)XieYongShun
    static function addUname($uname='', $mod='', $no=0){ 
        $tabid = 'users_uacc'; $key = "uname";
        if(empty($uname)){
            $uname = substr($mod,0,1).str_replace('-','',basKeyid::kidTemp('5'));
        }elseif(strpos($uname,'@') && strlen($uname)<20){
            // keep@
        }elseif(strlen($uname)>=20){ 
            $uname = substr($uname,0,4).'_'.substr($uname,-4).'_'.basKeyid::kidRand('24',4);
        }
        if($no){
            $uname .= '_'.basKeyid::kidRand('24',3); //echo $uname;
        }
        $r = glbDBObj::dbObj()->table($tabid)->field($key)->where("$key='$uname'")->find(); 
        if(!empty($r[$key])){ 
            if($no>5) die();
            return self::addUname($uname, $mod, $no+1);
        }
        return $uname;
    }
    
    static function addUid($uid=''){ 
        $tabid = 'users_uacc'; $key = "uid";
        if(empty($uid)){
            $kar = glbDBExt::dbAutID($tabid);
            $uid = $kar[0]; $uno = $kar[1];    
        }else{
            $uno = '1';    
        }
        $r = glbDBObj::dbObj()->table($tabid)->field($key)->where("$key='$uid'")->find(); 
        if(!empty($r[$key])){ 
            return self::addUid();
        }
        return array('uid'=>$uid,'uno'=>$uno);
    }
    
    static function bindUser($mname,$pptmod,$pptuid){ 
        glbDBObj::dbObj()->table('users_uppt')->data(array('uname'=>$mname, 'pptmod'=>$pptmod, 'pptuid'=>$pptuid))->insert();
    }

    static function delUser($uname, $key=0){
        // key:0-$uanme,1-uid,2-pptuid
        $umod = '';
        if($key==1){
            $uacc = db()->table('users_uacc')->where("uid='$uname'")->find();
            if(!empty($uacc)){
                $umod = $uacc['umods']; 
                $uname = $uacc['uname'];
            }else{
                $uname = '';
            }
        }elseif($key==2){
            $uppt = db()->table('users_uppt')->where("pptuid='$uname'")->find();
            if(!empty($uppt)){
                $uname = $uppt['uname'];
            }else{
                $uname = '';
            }
        }
        if(!$uname){
            return;
        }
        if($uname && empty($umod)){
            $uacc = db()->table('users_uacc')->where("uname='$uname'")->find(); 
            if(!empty($uacc)){
                $umod = $uacc['umods']; 
            }else{
                $umod = '';
            }
        }
        if(!empty($umod)){
            db()->table("users_$umod")->where("uname='$uname'")->delete();
        }
        db()->table('users_uacc')->where("uname='$uname'")->delete();
        db()->table('users_uppt')->where("uname='$uname'")->delete(); 
        db()->table('active_online')->where("uname='$uname'")->delete();
        db()->table('active_admin')->where("uname='$uname'")->delete();
        db()->table('active_login')->where("uname='$uname'")->delete();
        db()->table('active_login')->where("pptuid='$uname'")->delete();
    }

    static function chkExists($key,$val,$mod=''){ 
        $db = glbDBObj::dbObj();
        $_groups = glbConfig::read('groups');    
        if($key=='uname' && $re=basKeyid::keepCheck($val,1,1,1)){
            return $re;
        }
        $tmsg = '';
        if($key=='uname'){
            if($uinfo = $db->table("users_uacc")->where("uname='$val'")->find()){
                return basLang::show('plus.cajax_userid')."[$val](uacc)".basLang::show('plus.cajax_exsists');
            }
            if($mod && isset($_groups[$mod]) && $_groups[$mod]['pid']=='users'){
                if($uinfo = $db->table("users_$mod")->where("uname='$val'")->find()){
                    return basLang::show('plus.cajax_userid')."[$val]($mod)".basLang::show('plus.cajax_exsists');
                }
            }
        }elseif($key=='memail' || $key=='mtel'){
            if($key=='memail' && !basStr::isMail($val)){
                $tmsg = basLang::show('plus.cajax_mailid');
                return "Error $tmsg:[$val]!";
            };
            if($key=='mtel' && !basStr::isTel($val)){
                $tmsg = basLang::show('plus.cajax_telnum');
                return "Error $tmsg:[$val]!";
            };
            if($uinfo = $db->table("users_uppt")->where("pptmod='$key' AND pptuid='$val'")->find()){
                return $tmsg."[$val](uacc)".basLang::show('plus.cajax_exsists');
            }
        }
        return "success";
    }

}
