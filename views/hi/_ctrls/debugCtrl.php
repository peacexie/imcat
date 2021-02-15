<?php
namespace imcat\hi;

use imcat\basOut;
use imcat\basKeyid;
use imcat\comCookie;
use imcat\extWework;
use imcat\glbHtml;
use imcat\usrMember;
use imcat\vopApi;


use imcat\vopApi as api;

/*
*/ 
class debugCtrl extends uioCtrl{
    
    #public $ucfg = array();
    #public $vars = array();

    function __construct($ucfg=array(), $vars=array()){
        parent::__construct($ucfg, $vars);
        //$this->init($ucfg, $vars);
    }

    function homeAct(){
        $res = $this->re; // ['vars']
        //$res['newtpl'] = 'mhome';
        return $res;
    }

    function initDebug(){
        global $_cbase;
        //
    }

    function httpAct(){
        global $_cbase;
        $re = []; //$_GET;
        // data
        $re['ckey'] = $this->rlog['ckey']; 
        $re['server'] = $_SERVER["SERVER_NAME"]; 
        $re['name'] = $_cbase['sys_name'];
        $re['time'] = date('H:i:s'); //basKeyid::kidTemp();
        $re['sid'] = session_id();
        //$re['arr'] = ['a1'=>'b1','a2'=>'b2'];
        // dallow
        $dallow = req('dallow');
        if($dallow){
            $alp = '*'; glbHtml::dallow($alp);
        }
        // return;
        return api::view($re);
    }

    function t02Act(){

        require_once DIR_ROOT."/a3rd/wepv3/example/WxPay.Config.php";
        require_once DIR_ROOT."/a3rd/wepv3/WxPay.funcs.php";
        include_once DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php";
        require_once DIR_WEKIT."/js-sdk/lib/jssdk.php"; 

        $re = extWework::getUser('Peace123', 'AppCS');
        dump($re); die();

    }


    function t01Act(){

        $kidTemp = basKeyid::kidTemp(); dump($kidTemp);
        $ckey = usrMember::getCkey(); dump($ckey);

        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
        $wework = read('wework', 'ex');

        die();
        
        $CorpId = $wework['AppsConfig']['Fgedu']['CorpId'];
        $secret = $wework['AppsConfig']['Fgedu']['Secret'];

        //$CorpId = $wework['CorpId'];
        //$secret = $wework['AppsConfig']['AppCS']['Secret']; //$wework['TxlSecret'];

        $CorpId = $wework['AppsConfig']['T1Imcat']['CorpId'];
        $secret = $wework['AppsConfig']['T1Imcat']['Secret'];
        

        // getData
        $act = $this->ucfg['view'];
        $api = new \CorpAPI($CorpId, $secret);
        if($act=='deps'){
            $res = $api->DepartmentList(null, 1);
        }elseif($act=='utab'){
            //$res = $api->userSimpleList(1, 1, 1);
            $res = $api->UserList(1, 1, 1);

        }
        dump($res);

        die();
    }

    function test01(){

        $CorpId = read('wework.CorpId', 'ex');
        $agentId = $this->appId ?: 'AppCS'; //dump($agentId);
        $api = new \CorpAPI($CorpId, $agentId);
        $this->revars['peace'] = $api->GetUserById('XieYongShun');
    }


    /*
        ##########################################################
    */

    function apisTest($act, $token){ // $reurl, $scope, $state
        if($act=='udeps'){
            $res = extWeedu::getUserDeps($token); 
        }
        if($act=='deplist'){
            $res = extWeedu::getDeplist($token, ''); 
        }
        
        vopApi::view($res, 0);
        if(req('debug')){
            dump($this->re);
        }
        die('.End.');
    }


    function tdir1Act(){
        $retype = req('retype'); $res = $retype ? "retype=$retype" : 'p0=0';
        $pa = req('pa'); $pas = $pa ? "&pa=$pa" : '';
        $pb = req('pa'); $pbs = $pb ? "&pb=$pb" : '';
        $url = surl('login-tget1')."?$res$pas$pbs";
        glbHtml::dallow('*');
        header('Location:'.$url);
        die();
    }
    function tget1Act(){
        $re = $this->re['vars']['null'];
        $re['ucfg'] = $this->ucfg;
        return self::v($re);
    }

    function t2Act(){
        // set^XieYongShun.2020-b2-bewm.f9e6a3^0.0.0
        if(!empty($this->uinfo)){
            $stmp = ['set','XieYongShun.2020-av-hph6.e72f12'];
            $res = $this->saveSet($this->uinfo, $stmp, 'wecdir'); 
            dump($res);
            die('OK!');
        }else{
            die("Error-EmpytUser");
        }
    }

    function testAct(){

        $t = strlen(0);
        dump($t);

        $t = \imcat\usrMember::addUname('o9PAcuAerrObVtcXgKzXllG31twM', 'wechat');
        dump($t);

        $t = \imcat\usrMember::addUname('yufish', 'teeee');
        dump($t);

        $wecfg = wysBasic::getConfig('admin'); 
        $oid = 'oyDK8vjjcn2cFbxMLaMBhKEsYbCk';
        $weu = new wmpUser($wecfg);
        $user = $weu->getUserInfo($oid);
        dump($user);

        #$this->re['vars']['uinfo'] = $this->uinfo;
        #$cval = comConvert::sysRevert($utmp['uid'], 0, 'ck'); 

        die('-test-');
    }

}

/*

    // untie / pptuid
    function untieAct(){
        // db()->table('active_login')->where("ckey='{$this->ckey}'")->delete();
        vopApi::view($this->re['vars']);
    }
    function infoAct(){
        vopApi::view($this->re['vars']);
    }


*/
