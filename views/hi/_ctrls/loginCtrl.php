<?php
namespace imcat\hi;

use imcat\basEnv;
use imcat\basElm;
use imcat\basOut;
use imcat\basStr;

use imcat\comCookie;
use imcat\comConvert;
use imcat\glbDBExt;
use imcat\safComm;
use imcat\usrMember;

use imcat\vopApi as api;

/*
    wechat,wework,mobvc,idpwd,
    idcard,qq,email
*/ 
class loginCtrl extends uioCtrl{
    
    //protected $exp_day = 3; // 3天过期
    public $set_newtpl = 1;

    function __construct($ucfg=array(), $vars=array()){
        parent::__construct($ucfg, $vars);
        $this->init($ucfg, $vars);
    }

    function applyAct(){
        $this->applyBase();
        $re = &$this->re;
        if(!empty($re['vars']['uinfo'])){
            $vars = ['errtip'=>'已经登录', 'errmsg'=>'请先登出'];
            $vars = $this->uioVInfo($vars);
            return api::v($vars);
        }
        if(empty($re['vars']['umopt'])){
            $vars = ['errtip'=>'不允许注册', 'errmsg'=>'请联系管理员'];
            $vars = $this->uioVInfo($vars);
            return api::v($vars);
        }
        return api::v($re);
    }
    function appdoAct(){
        $this->applyBase();
        $re = &$this->re;
        if(empty($re['vars']['umopt'])){
            $vars = ['errtip'=>'不允许注册', 'errmsg'=>'请联系管理员'];
            $vars = $this->uioVInfo($vars);
            return api::v($vars);
        }
        $this->appdoBase();
        //sleep(1);
        api::v($re, 'api');
    }

    // (本地)登录测试: 判断权限再登录...
    function locinAct(){
        $vars = $this->re['vars'];
        $isLoc = basEnv::isLocal();
        $uname = empty($vars['uname']) ? '.^.' : $vars['uname'];
        #echo "($ucdebug,$uname)"; die();
        if($isLoc || strstr($vars['ucdebug'],$uname)){
            $this->locinBase();
        }else{
            api::v([], 'die', 'Error-Perm!');
        }
    }
    // logoutAct:绑定操作(重写)
    function logoutAct(){
        $vars = $this->re['vars']; 
        $uflag = $vars['uflag'];
        $res = $this->logoutBase();
        // mkv:跳转处理 
        $jpurl = $this->getJpurl();
        if($jpurl){
            //
        }else{
            $dbase = surl($this->mod);
            $jpurl = $uflag=='inmem' ? "$dbase?sec=wework" : ($uflag=='company' ? "$dbase?sec=wechat" : $dbase);
        }
        api::v($res, 'dir', $jpurl);
        #die($dir);
    }

    function homeAct(){
        $re = &$this->re;
        if(!$re['vars']['uflag']){ // 未登录:登录二维码
            $re['vars']['wx'] = $this->qrUrls("login-wecdir", 'snsapi_userinfo');
            $re['vars']['qy'] = $this->qrUrls("login-wewdir", 'snsapi_base');
        }else{ // 已登录
            // mkv:跳转处理 
            $jpurl = $this->getJpurl(); // die($jpurl);
            if($jpurl && $re['vars']['uflag']!='(bind)'){
                api::v([], 'dir', $jpurl);
            }
        } 
        $re['vars']['sec'] = $sec = req('sec', 'idpwd'); // full,wechat
        $re['vars']['dsec'] = in_array($sec,['idpwd','mobvc','wechat','wework']) ? $sec : 'idpwd'; 
        $re['vars']['nav2'] = req('nav2');
        return api::v($re);
    }

    function getJpurl($clear=1){
        $jpmkv = comCookie::oget('jpmkv'); 
        if($jpmkv && $jpmkv!=$this->ucfg['mkv']){
            $clear && comCookie::oset('jpmkv', ''); // clear; 
            $jpurl = basStr::isKey($jpmkv,3,32,'_.-@:') ? surl($jpmkv) : $jpmkv;
            return $jpurl;
        }
        return '';
    }

    // bindAct
    function bindAct(){
        $act = req('act');
        $umod = req('umod'); // 模型
        if($act=='save' && !empty($this->uinfo)){
            $cfg = ['grade'=>substr($umod,0,1).'com']; // 默认级别
            $urow = $this->bindBase($cfg);
        }
        $this->re['vars']['errmsg'] = '绑定成功!';
        if($act=='save'){ // svmod,jpmkv,bdkey
            // mkv:跳转处理 
            $jpurl = $this->getJpurl(); // die($jpurl);
            if($jpurl){
                api::v([], 'dir', $jpurl);
            }
        }
        api::view($this->re['vars']);
    }

    /*
        ##########################################################
    */

    // bind-check:绑定检查
    function bindCheck(&$row){ 
        $this->bindCheckBase($row);
        /*
        if(!empty($this->re['vars']['uimod']['company'])){
            $this->re['vars']['cscorp'] = data('cscorp.join',"did='{$this->re['vars']['uimod']['company']}'",1); 
        }*/
        return;
    }

    /*
        ##########################################################
    */

    // 扩展
    function eduidAct(){
        $this->eduidBase();
        $apido = req('apido'); $res = ''; 
        $token = empty($this->uinfo['mexa']['token']) ? '' : $this->uinfo['mexa']['token']; 
        if($apido && $token){
            $this->apisTest($apido, $token);
        }
        return $this->re;
    }

    function init($ucfg, $vars){    
        $re = &$this->re;
        if(!empty($re['vars']['uinfo'])){
            $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod']; 
            $uimod = $re['vars']['uimod']; $uname = empty($uimod['uname']) ? $uinfo['uname'] : $uimod['uname'];
            if($umod=='adminer'){ $this->re['vars']['ucdebug'] .= ",{$uname},"; } 
        }
    }

}

/*



*/
