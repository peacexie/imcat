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
    
    static function loginUser($rlog, $uname, $umod=''){
        if(!$umod){
            $uacc = $db->table('users_uacc')->where("uname='$uname'")->find();
            if(empty($uacc)){ return; }
            $umod = $uacc['umods'];         
        }
        $mtp = db()->table("users_$umod")->where("uname='$uname'")->find(); 
        $exm = [ // mname   mpic
            'mname' => empty($mtp['mname']) ? "($uname)" : $mtp['mname'],
            'mpic' => empty($mtp['mpic']) ? "" : $mtp['mpic'],
        ]; // mname    grade   mfrom   mtel    memail  miui 
        $row = ['uname'=>$uname,'umod'=>$umod] + $rlog + $exm;
        $tmp = db()->table('active_login')->data($row)->replace(0); 
    }

    static function bindUser($uname, $pptmod, $pptuid, $exins=[]){
        #glbDBObj::dbObj()->table('users_uppt')->data(array('uname'=>$uname, 'pptmod'=>$pptmod, 'pptuid'=>$pptuid))->replace();
        $idold = db()->table("users_uppt")->where("uname='$uname' AND pptmod='$pptmod'")->find(); 
        $dins = ['pptuid'=>$pptuid]; if(!empty($exins)){ $dins += $exins; }
        $dwhr = ['uname'=>$uname, 'pptmod'=>$pptmod];
        if(empty($idold)){
            db()->table("users_uppt")->data($dwhr+$dins)->insert(); 
        }else{
            db()->table("users_uppt")->data($dins)->where($dwhr)->update(); 
        }
    }

    // 更换模型-统一登录
    static function uexUser($uname, $tomod, $key=0, $ex2=[]){ // $row
        if(empty($uname)){ return []; }
        $uacc = db()->table('users_uacc')->where(($key?'uid':'uname')."='$uname'")->find();
        if(!empty($uacc)){
            $uid = $uacc['uid'];
            $umod = $uacc['umods'];
            $uname = $uacc['uname'];
        }else{
            return []; #die("Error-qexUser:$uname");
        }
        // 切换新模型
        $data1 = ['umods'=>$tomod];
        $tmp1 = db()->table('users_uacc')->data($data1)->where("uname='$uname'")->update(); 
        // 更新模型数据
        $old = db()->table("users_$umod")->where("uname='$uname'")->find();
        #$new = db()->table("users_$tomod")->where("uname='$uname'")->find();
        $fileds = read("$tomod.f"); $data2 = [];
        $data2 = self::umdData($fileds, $old, $ex2); //dump($data2);
        if(empty($new)){
            $data2['uid'] = $uid; $data2['uname'] = $uname;
            $tmp2 = db()->table("users_$tomod")->data($data2)->insert(); // ->where("uname='$uname'")
        }else{
            foreach($data2 as $fk=>$fr) {
                if(!strlen($fr)){ unset($data2[$fk]); }
            } 
            $tmp2 = db()->table("users_$tomod")->data($data2)->where("uname='$uname'")->replace();
        }
        // 删除旧模型
        $tmp3 = db()->table("users_$umod")->where("uname='$uname'")->delete();
        return ['old'=>$old, 'data2'=>$data2]; // 'new'=>$new, 
    }
    // 保存用户-统一登录
    static function usvUser(&$row, $mode, $cfgs, $upass=''){
        $umod = $row['umod']; 
        $_groups = glbConfig::read('groups'); 
        if(!isset($_groups[$umod]) || $_groups[$umod]['pid']!='users'){
            $re = ['errno'=>"Error-qsUser!",'errmsg'=>"model[$umod]Error!"];
            vopApi::view($re);
        }
        // uid, uname
        if(empty($row['uid'])){
            $tmp = usrMember::addUid();
            $uid = $tmp['uid']; $uno = $tmp['uno'];
        }else{
            $uid = $row['uid']; $uno = 1;
        }
        if(empty($row['uname']) && $mode && !in_array($mode,['locin','idpwd'])){
            $updUname = 1;
            if(!empty($row['mname']) && in_array($mode,['mobvc','qq'])){
                $uname = ($mode=='qq' ? 'qq_' : "mob_") . $row['pptuid'];
            }elseif(!empty($row['mname']) && $mode=='wework'){
                $uname = strlen($row['pptuid'])>15 ? basStr::filKey(comConvert::pinyinMain($row['mname'])) : $row['pptuid'];
            }elseif(!empty($row['mname']) && $mode=='wechat'){
                $uname = basStr::filKey(comConvert::pinyinMain($row['mname']));
            }elseif($mode=='edupr'){
                $uname = 'edu_'.$row['mname'].'_'.substr($row['pptuid'],-3);
            }elseif($mode=='extin'){
                $pre = empty($row['mname']) ? substr($row['pptuid'],0,4) : basStr::filKey(comConvert::pinyinMain($row['mname']));
                $uname = "out_{$pre}_".substr($row['pptuid'],-3);
            }else{
                $uname = empty($row['pptuid']) ? '' : $row['pptuid'];
            }
            $row['uname'] = usrMember::addUname($uname, $umod);
        }
        // uacc
        $dbpass = $upass ? comConvert::sysPass($row['uname'],$upass,$umod) : '(reset)';
        $acc = array('uid'=>$uid,'uno'=>$uno,'uname'=>$row['uname'],'upass'=>$dbpass,'umods'=>$umod,); 
        $fileds = read("$umod.f"); $dex = basSql::logData();
        db()->table('users_uacc')->data($acc+$dex)->insert(); 
        // umod
        $umd = ['uid'=>$uid,'uname'=>$row['uname']] + usrMember::umdData($fileds, [], $cfgs); 
        if(empty($umd['mname'])){ $umd['mname']=$row['mname']; }
        db()->table("users_$umod")->data($umd)->insert();   
        // pptuid
        if($mode && $mode!='idpwd' && !empty($row['pptuid'])){
            usrMember::bindUser($row['uname'], $mode, $row['pptuid']);
        }
        if(!empty($updUname) && !empty($row['pptuid'])){
            $tmp = db()->table('active_login')->data(['uname'=>$row['uname']])->where("pptuid='$row[pptuid]'")->update(0); 
        }
        return ['acc'=>$acc, 'umd'=>$umd, 'row'=>$row];
    }

    // 模型数据-统一登录
    static function umdData($fileds, $ex1=[], $ex2=[], $gs=1){
        if($gs){
            $fileds['grade'] = ['dbtype'=>'varchar', 'dbdef'=>''];
            $fileds['show'] = ['dbtype'=>'int', 'dbdef'=>'0'];
        }
        $obj = []; 
        $nt = ['float', 'decimal', 'double'];
        foreach($fileds as $fk=>$fv) {
            if($fv['dbtype']=='nodb'){ continue; }
            if(isset($ex2[$fk])){
                $obj[$fk] = $ex2[$fk];
            }elseif(isset($ex1[$fk])){
                $obj[$fk] = $ex1[$fk];
            }else{
                if(strlen($fv['dbdef'])>0){
                    $obj[$fk] = $fv['dbdef'];  
                }elseif(in_array($fv['dbtype'],$nt)||strstr($fv['dbtype'],'int')){
                    $obj[$fk] = 0;
                }else{
                    $obj[$fk] = '';
                }
            }
        }
        return $obj;
    }
    // 模型数据-统一登录
    static function umdEdit($umod, $data=[]){
        $fileds = is_array($umod) ? $umod : read("$umod.f");
        $obj = []; 
        $nt = ['float', 'decimal', 'double'];
        foreach($fileds as $fk=>$fv) {
            if($fv['dbtype']=='nodb'){ continue; }
            if(isset($data[$fk])){
                $obj[$fk] = $data[$fk];
            }
        }
        return $obj;
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

    /*
        ### from uioCtrl #######################################################
    */

    // wechat-0, locin-0, eduid-uio
    static function getCkey($skey='login-uio'){
        $ckey = req('_ckey');
        if($ckey){ 
            $carr = explode('.', "$ckey.");
            if($carr[0]==substr(md5($carr[1]),4,8)){
                return $ckey;
            }
        }
        //$udefs = read('udefs', 'sy'); 
        //$_ckss = empty($udefs['_ckss']) ? [] : $udefs['_ckss'];
        $ckk = empty($skey) ? 'login-uio' : $skey; //dump($ckk);
        $ckey = comSession::getCook($ckk);
        $ckey = substr(md5($ckey),4,8).".$ckey";
        return $ckey;
    }

}
