<?php
namespace imcat\hi;

use imcat\basEnv;
use imcat\basElm;
use imcat\basOut;
use imcat\basReq;
use imcat\basStr;

use imcat\comCookie;
use imcat\comConvert;
use imcat\extWework;
use imcat\extWeedu;
use imcat\glbDBExt;
use imcat\usrMember;

use imcat\vopApi as api;

/*

*/ 
class usetCtrl extends uioCtrl{
    
    //protected $exp_day = 3; // 3天过期

    function __construct($ucfg=array(), $vars=array()){
        parent::__construct($ucfg, $vars);
        $this->init($ucfg, $vars);
    }

    function cfgsAct(){
        $re = &$this->re; //dump($this->ckey);
        $re['vars']['view'] = $this->view; // req('sec','info');
        $uname = $re['vars']['uname'];
        
        return api::v($re);
    }

    function reinAct(){
        $re = &$this->re; //dump($this->ckey);
        $re['vars']['view'] = $this->view; // req('sec','info');
        $uname = $re['vars']['uname'];
        $re['vars']['weact'] = $weact = req('weact');
        if($this->view=='wework' && $weact){
            extWework::updContacts($weact);
            $re['vars']['data'] = extWework::getContacts($weact);
        }elseif($this->view=='wework'){
            $re['vars']['deps'] = extWework::getContacts('deps');
            $re['vars']['utab'] = extWework::getContacts('utab');
            $dep = req('dep');
            $re['vars']['dep'] = $dep ? $dep : '0';
        }
        return api::v($re);
    }
    function doreinAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        $re['vars']['errmsg'] = '登录成功，请刷新！';
        // 
        $uname = req('uname');
        $view = req('view'); $view || $view = 'system';
        if(!$uname){
            $re['vars']['errno'] = 'Empty-Uname'; 
            $re['vars']['errmsg'] = '账号为空'; 
        }elseif($view=='system'){
            $tmp = db()->table('users_uacc')->where("uname='$uname'")->find();
            if(!empty($tmp)){ // 取会员信息
                usrMember::loginUser($this->rlog, $uname, $tmp['umods']); // , $umod=''
            }else{
                $re['vars']['errno'] = 'Error-Uname'; 
                $re['vars']['errmsg'] = '账号错误'; 
            }
        }elseif($view=='demo'){
            $udemo = read('udemo', 'sy'); 
            if(isset($udemo[$uname])){ // 取预设信息
                $row = $this->rlog + $udemo[$uname];
                $row['mext'] = '';
                $mode = req('mode', 'locin');
                if($mode!='locin'){ $this->rlog['utype'] = $row['utype'] = $mode; }
                $this->saveLogin($row, $mode);
            }else{ 
                $re['vars']['errno'] = 'Error-Uname'; 
                $re['vars']['errmsg'] = '账号错误'; 
            }
        }elseif($view=='wework'){
            include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
            $agentId = 'AppCS';
            $CorpId = read('wework.CorpId', 'ex');
            $api = new \CorpAPI($CorpId, $agentId);
            $user = $api->GetUserById($uname); 
            $ext = "gender={$user['gender']}".(empty($user['mobile']) ? '' : "\nmtel={$user['mobile']}");
            $utmp = ['pptuid'=>$user['userid'], 'mname'=>$user['name'], 'mpic'=>$user['avatar'], 'mext'=>$ext];
            $urow = $utmp + $this->rlog;
            $this->saveLogin($urow, 'wework');        
        }
        return api::v($re, 'api');
    }

    function minfoAct(){
        $re = &$this->re; //dump($this->ckey);
        $re['vars']['view'] = $this->view; // req('sec','info');
        $uname = $re['vars']['uname'];
        //*
        if($this->view=='exmod'){
            $umods = $this->re['vars']['umods'];
            foreach ($umods as $um => $uval) {
                if($um==$re['vars']['uflag'] || $um=='adminer'){
                    unset($umods[$um]);
                }
            }
            $re['vars']['umopt'] = basElm::setOption($umods, '', 0);
        } //*/
        if($this->view=='bdmob'){ // 
            $mobvc = db()->table("users_uppt")->where("uname='$uname' AND pptmod='mobvc'")->find(); 
            $re['vars']['mobvc'] = $mobvc; //dump($mobvc);
        } 
        if($this->view=='bdwx'){ // 
            $wechat = db()->table("users_uppt")->where("uname='$uname' AND pptmod='wechat'")->find(); 
            $re['vars']['wechat'] = $wechat; //dump($wechat);
            // qrs
            $qrs = $this->qrUrls("login-wecdir", 'snsapi_userinfo', $uname);
            $qrs['linkUrl'] = str_replace('=dir^', '=bind^', $qrs['linkUrl']);
            $qrs['scanUrl'] = str_replace('=scan^', '=bind^', $qrs['scanUrl']);
            $re['vars']['qrs'] = $qrs;
        } 
        if($this->view=='idcard'){ // 
            $idcard = db()->table("users_uppt")->where("uname='$uname' AND pptmod='idcard'")->find(); 
            $re['vars']['idcard'] = $idcard; //dump($idcard);
        } 
        if($this->view=='expw'){ // 
            $uacc = db()->table("users_uacc")->where("uname='$uname'")->find(); 
            $re['vars']['ocheck'] = $uacc['upass']; //dump($uacc);
        }
        return api::v($re);
    }

    function svbdwxAct(){
        global $_cbase;
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        // 
        $act = req('act');
        if($act=='unbind' && $uinfo['utype']=='wechat'){
            $re = ['errno'=>'Error-Type', 'errmsg'=>'当前登录方式不能解绑'];
            return api::v($re, 'api');
        }elseif($act=='unbind'){ // TODO:
            $re['vars']['errmsg'] = '解绑成功';
            db()->table("users_uppt")->where("uname='$uname' AND pptmod='wechat'")->delete(); 
        }else{
            $re = ['errno'=>'Error-VODE', 'errmsg'=>'短信码错误'];
        }
        return api::v($re, 'api');
    }

    function svbdmobAct(){
        global $_cbase;
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        $re['vars']['errmsg'] = '绑定成功';
        // 
        #safComm::urlFrom();
        $mcode = req('mcode'); $mtel = req('mtel');
        $act = req('act');
        if($act=='unbind' && $uinfo['utype']=='mobvc'){
            $re = ['errno'=>'Error-Type', 'errmsg'=>'当前登录方式不能解绑'];
            return api::v($re, 'api');
        }elseif($act=='unbind'){ // TODO:
            $re['vars']['errmsg'] = '解绑成功';
            db()->table("users_uppt")->where("uname='$uname' AND pptmod='mobvc'")->delete(); 
            return api::v($re, 'api');
        }
        // db-check
        $stamp = $_cbase['run']['stamp']-600; 
        $rdb = db()->table('plus_smsend')->where("tel='$mtel' AND pid='mobvc:$mcode' AND atime>='$stamp' ")->find();
        if(!empty($rdb['res']) && $rdb['res']=='1:OK'){
            db()->table("plus_smsend")->data(array('pid'=>"ok-vcode:$mcode"))->where("kid='{$rdb['kid']}'")->update(0);
            $idold = db()->table("users_uppt")->where("uname='$uname' AND pptmod='mobvc'")->find(); 
            usrMember::bindUser($uname, 'mobvc', $mtel);
        }else{
            $re = ['errno'=>'Error-VODE', 'errmsg'=>'短信码错误'];
        }
        return api::v($re, 'api');
    }

    function svexpwAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        $re['vars']['errmsg'] = '修改成功';
        /*/ 
        $opass = req('opass'); $ocheck = req('ocheck');
        $oenc = comConvert::sysPass($uname,$opass,$umod);
        if($ocheck!='(reset)' && $oenc!==$ocheck){
            $re['vars']['errno'] = 'Error-Password'; 
            $re['vars']['errmsg'] = '旧密码不一致'; 
            return api::v($re, 'api');
        }*/
        // 
        $mname = req('mname');
        if($mname!=$uimod['mname']){
            $re['vars']['errno'] = 'Error-userName'; 
            $re['vars']['errmsg'] = '姓名不一致'; 
            return api::v($re, 'api');
        }
        //
        $npass = req('npass'); $ncheck = req('ncheck');
        if(!$npass && $npass!==$ncheck){
            $re['vars']['errno'] = 'Error-Password'; 
            $re['vars']['errmsg'] = '密码为空或不一致'; 
            return api::v($re, 'api');
        }
        //
        $nenc = comConvert::sysPass($uname,$npass,$umod);
        db()->table("users_uacc")->data(['upass'=>$nenc])->where("uname='$uname'")->update(); 
        // 
        return api::v($re, 'api');
    }

    function svidcardAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        $re['vars']['errmsg'] = '绑定成功';
        // 
        $act = req('act');
        if($act=='unbind'){
            $re['vars']['errmsg'] = '解绑成功';
            db()->table("users_uppt")->where("uname='$uname' AND pptmod='idcard'")->delete(); 
        }else{
            $mname = req('mname'); $idcard = req('idcard');
            $exins = ['auser'=>$mname];
            usrMember::bindUser($uname, 'idcard', $idcard, $exins);
        }
        return api::v($re, 'api');
    }
    function svudelAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        $re['vars']['errmsg'] = '注销成功';
        // 
        $mname = req('mname');
        if($mname!=$uimod['mname']){
            $re['vars']['errno'] = 'Error-userName'; 
            $re['vars']['errmsg'] = '姓名不一致'; 
            return api::v($re, 'api');
        }
        usrMember::delUser($uname, 0);
        //usrMember::delUser($uname, 2);
        return api::v($re, 'api');
    }
    function svexmodAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        $re['vars']['errmsg'] = '更换成功';
        // 
        $tomod = req('tomod');
        if(!$tomod){ 
            $re['vars']['errno'] = 'Error-userMod'; 
            $re['vars']['errmsg'] = '用户类型不合法'; 
            return api::v($re, 'api');
        }
        usrMember::uexUser($uname, $tomod);
        $t4 = db()->table("active_login")->data(['umod'=>$tomod])->where("ckey='$this->ckey'")->update(); 
        // TODO: 重设密码
        return api::v($re, 'api');
    }
    function svexidAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; 
        $re['vars']['errmsg'] = '更换成功';
        // uname
        $uname = basStr::filKey(req('uname'),'_@.-'); // 
        if(!$uname || strlen($uname)<5){ 
            $re['vars']['errno'] = 'Error-userName'; 
            $re['vars']['errmsg'] = '用户名不合法'; 
            return api::v($re, 'api');
        }
        // 占用
        $uold = $uimod['uname'];
        $unew = usrMember::addUname($uname, $umod);
        if($unew==$uold || $unew!=$uname){
            $re['vars']['errno'] = 'Error-userName'; 
            $re['vars']['errmsg'] = '用户已占用'; 
            return api::v($re, 'api');
        }
        // update
        $data = ['uname'=>$unew];
        $t1 = db()->table("users_uacc")->data($data)->where("uname='$uold'")->update(); 
        $t2 = db()->table("users_$umod")->data($data)->where("uname='$uold'")->update(); 
        $t3 = db()->table("users_uppt")->data($data)->where("uname='$uold'")->update(); 
        $t4 = db()->table("active_login")->data($data)->where("ckey='$this->ckey'")->update(); 
        $whr5 = "ckey='$this->ckey' AND pptuid='$uold'";
        //$t5 = db()->table("active_login")->data(['pptuid'=>$unew])->where($whr5)->update(); 
        return api::v($re, 'api');
    }
    function svbaseAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        $re['vars']['errmsg'] = '保存成功';
        // 
        $fm = basReq::arr('fm');
        $data = usrMember::umdEdit($umod, $fm); 
        $re['vars']['data'] = $data;
        if(empty($data)){ 
            $re['vars']['errno'] = 'Empty-Form'; 
            $re['vars']['errmsg'] = '提交资料为空'; 
        }
        $t1 = db()->table("users_$umod")->data($data)->where("uname='$uname'")->update(); 
        return api::v($re, 'api');
    }

    function homeAct(){
        $re = &$this->re;
        return api::v($re);
    }
    function init($ucfg, $vars){
        $re = &$this->re;
        if(empty($re['vars']['uimod'])){
            header('Location:'.surl('login'));
            die('init');
        }
        $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod'];
        $uimod = $re['vars']['uimod']; $uname = $uimod['uname'];
        if($umod=='adminer'){ $this->re['vars']['udebug'] .= ",{$uname},"; }
        //
        $can_debug = strstr($re['vars']['udebug'], $re['vars']['uname']);
        $need_debug = in_array($this->key, ['rein', 'dorein']);
        if($need_debug&&(!$can_debug)){
            //return api::v($re, 'dir', 'login');
            header('Location:'.surl('login'));
            die('init');
        }
        $re['newtpl'] = $this->mod.'/'.($this->key?$this->key:'mhome');
        #usleep(100000);
    }

}

/*



*/
