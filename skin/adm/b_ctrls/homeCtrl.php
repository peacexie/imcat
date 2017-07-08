<?php
/*
*/ 
class homeCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        //$this->_init();
    }

    //function _init(){}
    
    function homeAct(){
        $user = $this->_cloginAct(1);
        $userstr = "<span title='".@$user->uperm['title']."(".@$user->uperm['grade'].")'>Hi:".$user->usess['uname']."!</span>";
        $vars['userstr'] = $userstr;
        if($mke=basReq::val('mke')){
            $reurl = comParse::urlBase64($mke,1);
        }
        $mainurl = vopUrl::fout(0).(empty($reurl) ? '?uhome' : $reurl);
        $vars['mainurl'] = $mainurl;
        $re = array('vars'=>$vars); 
        if(basEnv::isMobile()){
            $re['newtpl'] = 'frame/awtop-mob';
        }
        return $re; 
    }

    function uhomeAct(){
        $user = $this->_cloginAct();
        $vars['db'] = glbDBObj::dbObj();
        $vars['exinfo'] = req('exinfo');
        $act = req('act');
        if($act=='update'){
          $arr = array('client-cn','client-en','server-cn','server-en'); 
          foreach($arr as $key){ @unlink(DIR_DTMP."/dset/_upd_$key.htm"); }
        }elseif($act=='modstat'){
          $arr = array('modstat-cn','modstat-en'); 
          foreach($arr as $key){ @unlink(DIR_DTMP."/dset/_upd_$key.htm"); }
        }elseif($act=='uspace'){
          $arr = array('spaceinfo-cn','spaceinfo-en'); 
          foreach($arr as $key){ @unlink(DIR_DTMP."/dset/_upd_$key.htm"); }
        }
        $vars['act'] = $act; 
        return array('vars'=>$vars);
    }
    
    // 
    function _cloginAct($sess=0){
        $user = usrBase::userObj('Admin');
        if($user->userFlag=='Guest') header('Location:'."?login");
        $sess && $user->setSess();
        return $user;
    }
    
}
