<?php
namespace imcat\adm;

use imcat\basEnv;
use imcat\basJscss;
use imcat\basReq;
use imcat\comParse;
use imcat\glbConfig;
use imcat\glbDBObj;
use imcat\usrBase;
use imcat\vopUrl;

class homeCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        //$this->_init();
    }

    //function _init(){}
    
    function homeAct(){
        $user = $this->_cloginAct(1);
        $userstr = "<span title='".@$user->uperm['title']."(".@$user->uperm['grade'].")'>Hi:".$user->usess['uname']."!</span>";
        $vars['user'] = $user;
        $vars['userstr'] = $userstr;
        $mainurl = vopUrl::fout(0).'?uhome';
        $vars['mainurl'] = $mainurl;
        $re = array('vars'=>$vars); 
        if(basEnv::isMobile()){
            $re['newtpl'] = 'frame/awtop-mob';
        }
        return $re; 
    }

    function uhomeAct(){
        $vars['db'] = glbDBObj::dbObj();
        $user = $this->_cloginAct();
        $vars['user'] = $user;
        $vars['exinfo'] = req('exinfo');
        $act = req('act');
        if($act=='update'){
          $arr = array('server-cn','server-en'); // 'client-cn','client-en',
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

    # copy from texAdtop.php

    static function adm_jscfgs(){
        $jscfg = "\n// js Admin"; 
        $jscfg .= "\nvar _miadm={}, _mpadm={}; ";
        $user = usrBase::userObj('Admin');
        $imenu = '';
        if(!empty($user)){
            if(!empty($user->uperm['impid'])){ // 继承菜单
                $grades = glbConfig::read('grade','dset');
                if(isset($grades[$user->uperm['impid']])){
                    $imenu = $grades[$user->uperm['impid']]['pmadm'];
                }
            }
            $jscfg .= "\n_miadm.userType = '".$user->userType."';";
            $jscfg .= "\n_miadm.userGrade = '".@$user->uperm['grade']."';"; 
            $jscfg .= "\n_miadm.userFlag = '".$user->userFlag."';";
            $jscfg .= "\n_miadm.uname = '".@$user->usess['uname']."';";
            $jscfg .= "\n_mpadm.title = '".@$user->uperm['title']."';";
            $jscfg .= "\n_mpadm.menus = ':,".@$user->uperm['pmadm'].",$imenu,';";
            $jscfg .= "\n_mpadm.defmu = '".@$user->uperm['defmu']."';";
        }
        $jscfg .= "\n";
        echo basJscss::jscode($jscfg);
    }

}
