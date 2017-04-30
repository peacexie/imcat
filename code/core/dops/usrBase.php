<?php

// usrBase
class usrBase{    

    public $userType = ''; //member/adminer
    public $userFlag = 'Guest'; //Login/Guest,Error
    public $usTable = ''; //online/admin
    public $utmOut = ''; //1200/2400
    public $udbUpd = 300; //db更新周期，5分钟
    //public $uckUpd = 20; //cookie更新周期，20秒
    public $errno = 0;
    public $sessid = '';
    
    public $sinit = array(); //初始sid,sip,sua数据
    public $usess = array(); //active会话数据
    public $uperm = array(); //用户权限
    public $uinfo = array(); //用户模型数据
    
    static $uobjs = array(); //用户对象
    public $db = NULL;
    
    function __construct($userType=''){
        $this->db = db(); 
        $this->sess_tout($userType);
        $this->sess_init();
        $this->check_cuser();
    }
    
    // re : Null, isLogin, Forbid, Info, array()
    function check_logout(){
        $_groups = read('groups');
        if($this->userFlag=='Error') return 'Forbid';
        if($this->userFlag!='Login') return 'notLogin'; 
        $this->userFlag = 'Guest';
        $this->uperm = array();
        $this->uinfo = array();
        $data = $this->sinit; $sid = $data['sid']; unset($data['sid'],$data['scode']);
        foreach(array('show','cfgs','grade') as $k){
            $data[$k] = $this->usess[$k] = '0';
        }
        $this->usess['errno'] = '0';
        $this->db->table($this->usTable)->data(basReq::in($data))->where("sid='$sid'")->update();
        return 'OK'; 
    }
    
    function login_msg($key){
        if(is_numeric($key)){
            $re = lang('usrb_ertimes',$key);
        }else{
            $ucfg = basLang::ucfg('cfglibs.usrbase');
            $re = isset($ucfg[$key]) ? $ucfg[$key] : "($key)".lang('usrb_erunknow');
        }
        return $re;
    }
    
    //
    static function setLogin($type='m', $uname=''){
        $db = db();
        if(empty($uname)) return;
        $user = self::userObj($type=='m' ? 'Member' : 'Admin');
        $user->uinfo = $user->uget_minfo($uname); 
        $data = $user->sinit; unset($data['scode']); 
        $data['uname'] = $user->uinfo['uname']; 
        $data['cfgs'] = '0';
        $data['grade'] = $user->uinfo['grade']; //grade=xstop处理???
        $data['errno'] = 0;
        $data['show'] = empty($user->uinfo['show']) ? '0' : $user->uinfo['show'];
        $usTable = $type=='m' ? 'active_online' : 'active_admin';
        if(!empty($user->usess)){
            $sid = $data['sid']; unset($data['sid']);
            $db->table($usTable)->data(basReq::in($data))->where("sid='$sid'")->update();
        }else{ 
            $db->table($usTable)->data(basReq::in($data))->insert();
        } 
    }
    
    // re : Null, isLogin, Forbid, Info, array()
    function check_login($uname='',$upass=''){
        $_groups = read('groups');
        if(empty($uname) || empty($upass)) return 'Null'; 
        $simpass = read('simpass','sy');
        if(in_array($upass,$simpass)) return 'SimPass'; 
        if($this->userFlag=='Error') return 'Forbid';
        if($this->userFlag=='Login') return 'isLogin'; 
        $uname = basStr::filKey($uname,'_'); 
        $this->uinfo = $this->uget_minfo($uname,$upass); 
        if(empty($this->uinfo['show'])) return 'noChecked'; 
        if($this->uinfo['grade']=='isStopped') return 'isStopped'; //grade=xstop处理???
        if($this->uinfo['grade']=='unActivated') return 'unActivated'; 
        $data = $this->sinit; unset($data['scode']);
        $data['uname'] = $uname; $data['show'] = '0'; $data['cfgs'] = '0';
        if($this->uinfo){ 
            $this->userFlag = 'Login';
            $data['grade'] = $this->uinfo['grade']; 
            $data['errno'] = 0;
            $data['show'] = empty($this->uinfo['show']) ? '0' : $this->uinfo['show'];
            $this->uperm = $this->uget_perms($this->uinfo['grade']); 
            $this->uset_mlogs($uname,$this->uperm['model']);
            $re = 'OK';
        }else{ 
            $data['grade'] = '0';
            $data['errno'] = empty($this->usess) ? 1 : $this->usess['errno'] + 1;
            $re = $data['errno'];
        } 
        if(!empty($this->usess)){
            $sid = $data['sid']; unset($data['sid']);
            $this->db->table($this->usTable)->data(basReq::in($data))->where("sid='$sid'")->update();
        }else{ 
            $this->db->table($this->usTable)->data(basReq::in($data))->insert();
        }
        $this->usess = $data; 
        return $re;
    }
    
    //$re //Login/Guest,Error
    function check_cuser(){
        $stamp = $_SERVER["REQUEST_TIME"];
        $_groups = read('groups');
        $sid = $this->sinit['sid'];
        $this->usess = $this->uget_online($sid,'*'); 
        if(!empty($this->usess)){ // 判断: stime,errno,uid,grade,
            if($stamp-$this->usess['stime']>$this->utmOut){ //超时
                $this->userFlag = 'Guest';
            }elseif($this->usess['errno']>=$this->errno){ //错误
                $this->userFlag = 'Error';
            }else{ //登录正常
                $this->uperm = $this->uget_perms($this->usess['grade']);
                if($this->uperm){
                    $this->userFlag = 'Login';
                    if($stamp-$this->usess['stime']>$this->udbUpd){ //更新会话表
                        $data = array('stime'=>$stamp);
                        $this->db->table($this->usTable)->data(basReq::in($data))->where("sid='$sid'")->update();
                    }
                }
                $this->uinfo = $this->uget_minfo($this->usess['uname']); //if(empty($this->uinfo['show']))  
            }        
        }
    }

    //...???
    function uget_online($sid,$field=''){
        $_groups = read('groups');
        $field || $field = '*';
        $res = $this->db->table($this->usTable)->field($field)->where("sid='$sid'")->find();
        return $res;    
    }

    //...
    static function uget_perms($grade){
        $grades = read('grade','dset');
        if(isset($grades[$grade])){
            $res = $grades[$grade];
        }else{
            $res = array();    
        }
        return $res;    
    }

    //...
    function uset_mlogs($uname,$umod,$user=''){
        global $_cbase;
        $_cbase['run']['skipDemo'] = 1;
        $user || $user = $uname;
        $data = array('eip'=>$this->sinit['sip'],'etime'=>$this->sinit['stime'],'euser'=>$user);
        $re1 = $this->db->table("users_$umod")->data(basReq::in($data))->where("uname='$uname'")->update();
        //$re2 = $this->db->table("users_uacc")->data(array('etime'=>$this->sinit['stime']))->where("uname='$uname'")->update();
        $_cbase['run']['skipDemo'] = 0;
    }

    //..., model,grade,checked
    static function uget_minfo($uname,$upass='',$unset=array('upass')){
        $db = db(); 
        $_groups = read('groups');
        $ubase = $db->table('users_uacc')->where("uname='$uname'")->find(); 
        $umod = $ubase['umods']; $dbpass = $ubase['upass'];
        if(!isset($_groups[$umod])) return array(); 
        if($upass && $dbpass!==comConvert::sysPass($uname,$upass,$umod)) return array();
        $uminf = $db->table("users_$umod")->where("uname='$uname'")->find(); // AND `show`='1'
        if(empty($uminf)) return array();
        if(!empty($ubase)){
            if(!empty($unset)){ foreach($unset as $k){
                unset($ubase[$k]);
            } }
            $uminf = $uminf + $ubase;
        } 
        return $uminf;    
    }
    
    //超时时间设置
    function sess_tout($userType=''){
        global $_cbase; 
        $madmin = intval(@$_cbase['tout_admin']);
        $member = intval(@$_cbase['tout_member']);
        //小时
        if($madmin<0.15) $madmin = 1; if($madmin>100) $madmin = 96; 
        if($member<0.15) $member = 4; if($member>100) $member = 96; 
        $this->userType = $userType;
        $this->usTable = $this->userType=='adminer' ? 'active_admin' : 'active_online'; 
        $this->errno = $this->userType=='adminer' ? 5 : 8; 
        $this->utmOut = 3600*($this->userType=='adminer' ? $madmin : $member);

        
    }
    //初始化seesion
    function sess_init(){
        global $_cbase; 
        $this->sessid = usrPerm::getSessid();
        $stime = $_cbase['run']['stamp'];
        $scode = comConvert::sysEncode($stime,$_cbase['safe']['safil']);
        $re = comSession::guid('safil','sessid');
        foreach(array('stime','scode') as $k) $re[$k] = $$k;
        $this->sinit = $re; 
    }
        
    // $uclass : 按uclass建立user对象, <Null>-Admin/Member-建立
    static function userObj($uclass=''){
        if(empty($uclass)){ 
            $uclass = defined('RUN_ADMIN') ? 'Admin' : array('Member','Admin');    
        }
        if(is_array($uclass)){
            $utmp = NULL;
            foreach($uclass as $flag){
                $iuser = self::userObj($flag);
                if($flag=='Member') $utmp = $iuser;
                if(!empty($iuser->uperm)){ 
                    return $iuser;
                }
            }
            return $utmp;
        }
        $uclass = $uclass=='Admin' ? 'usrAdmin' : 'usrMember';
        if(empty(self::$uobjs[$uclass])){ 
            self::$uobjs[$uclass] = new $uclass();
        }
        return self::$uobjs[$uclass];
    }
    
}
