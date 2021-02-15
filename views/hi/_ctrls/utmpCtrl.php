<?php
namespace imcat\hi;

use imcat\basEnv;
use imcat\basElm;
use imcat\basOut;
use imcat\basReq;
use imcat\basStr;

use imcat\comCookie;
use imcat\comConvert;
use imcat\extCache;
use imcat\extWework;
use imcat\extWeedu;
use imcat\glbDBExt;
use imcat\usrMember;

use imcat\vopApi as api;

/*

*/ 
class utmpCtrl extends uioCtrl{
    
    //protected $exp_day = 3; // 3天过期

    function __construct($ucfg=array(), $vars=array()){
        parent::__construct($ucfg, $vars);
        $this->init($ucfg, $vars);
    }

    function minfoAct(){
        $re = &$this->re; //dump($this->ckey);
        $re['vars']['view'] = $this->view; //dump($re);
        #$uname = $re['vars']['uname']; 
        //*
        return api::v($re);
    }
    function svbaseAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $re['vars']['errmsg'] = '保存成功';
        // 
        $fm = basReq::arr('fm');
        if(empty($fm)){ 
            $re['vars']['errno'] = 'Empty-Form'; 
            $re['vars']['errmsg'] = '提交资料为空'; 
        }else{
            $cac = new extCache(); $caexp = ($this->exp_day*2)*86400;
            $cac->set('ut_'.$re['vars']['uname'], $fm, $caexp);
        }
        usleep(200000);
        return api::v($re, 'api');
    }

    function homeAct(){
        $re = &$this->re;
        return api::v($re);
    }
    function init($ucfg, $vars){
        $re = &$this->re;
        if(empty($re['vars']['uinfo'])){
            header('Location:'.surl('login'));
            die('init');
        } 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $re['newtpl'] = $this->mod.'/'.($this->key?$this->key:'mhome');
        //
        $cac = new extCache(); $caexp = ($this->exp_day*2).'d';
        $mc = $cac->get('ut_'.$re['vars']['uname']);
        $uimod['mname']  = empty($mc['mname']) ?  '' : $mc['mname'];
        $uimod['mtel']   = empty($mc['mtel']) ?   '' : $mc['mtel'];
        $uimod['memail'] = empty($mc['memail']) ? '' : $mc['memail'];
        $re['vars']['uimod'] = $uimod; //dump($mc); dump($re['vars']['uname']);
    }

}

/*

mname,mtel,memail

*/
