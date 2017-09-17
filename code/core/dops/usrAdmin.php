<?php
(!defined('RUN_INIT')) && die('No Init');

// usrAdmin
class usrAdmin extends usrBase{    
    
    //public $sessid = '';
    
    function __construct() {
        parent::__construct('adminer'); 
    }
    
    //
    function login($uname='',$upass='',$ck=0){
        $re1 = $this->check_login($uname,$upass);
        $re2 = $this->login_msg($re1);
        if($re1=='OK'){ //Session
            $this->setSess();
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
    
    function setSess(){
        $str = '';
        foreach(array('model','grade') as $k){
            $str .= (empty($str) ? '':'&')."$k=".@$this->uperm[$k];
        } 
        // model=adminer&grade=supper 
        if(strpos($str,'grade=supper')){
            $str .= "&pextra=pstools,psdev";
        }else{
            $str .= "&pextra=".@$this->uperm['pextra'];    
        } 
        comSession::set($this->sessid,$str); 
    }

    static function opLogin($vop=null){    
        $user = usrBase::userObj('Admin');
        $act = basReq::val('act');
        if($act=='dologin'){ 
            $re2 = safComm::formCAll('fmadm'); 
            if(empty($re2[0])){ 
                $fm = $_POST['fm'];
                $res = $user->login($fm['uname'],$fm['upass']);
                $remsg = $res[0]=='OK' ? '' : $res[1];
                $remsg || header('Location:'."?");    
            }else{
                $remsg = basLang::show('admin.oplogin_vform_err');    
            } 
        }elseif($vop->key=='logout'){ 
            $user->logout();
            $remsg = basLang::show('admin.oplogin_logout');    
        }
        $remsg = empty($remsg) ? basLang::show('admin.oplogin_please_login') : $remsg; 
        return $remsg;
    } 

}
