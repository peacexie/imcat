<?php
namespace imcat\umc;

use imcat\basEnv;
use imcat\basElm;
use imcat\basKeyid;
use imcat\basMsg;
use imcat\comConvert;
use imcat\comCookie;
use imcat\basOut;
use imcat\basReq;
use imcat\extWework;
use imcat\glbDBExt;
use imcat\glbHtml;
use imcat\vopShow;

use imcat\vopApi as api;

/*
*/ 

class equipCtrl extends bcsCtrl{

    #public $re = [];

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        parent::__construct($ucfg, $vars);
        $this->init($ucfg, $vars);
    }

    function homeAct(){
        $re = &$this->re;
        $re['vars']['whrstr'] = "";
        //$re['newtpl'] = 'equip/mhome';
        $f1 = $re['vars']['uflag']=='inmem'; //dump([$csno, $key]); 
        if(!$f1){ 
            $re['vars']['errno'] = "Error-Custom-Perm";
            $re['vars']['errtip'] = "无权限操作";
            $re['vars']['errmsg'] = "无客户列表操作权限 : 请重新登录为员工！";
            $re['newtpl'] = 'home/info'; // 设置模板
            return api::v($re);
        }  
        return api::v($re);
    }

    function bqrAct(){
        $re = &$this->re; 
        $uname = $re['vars']['uname']; //tex('texBase')->wewIdop(); //req('uname');
        $did = req('did'); $atime = time();
        $ckey = "$uname.$did.".substr(md5("$uname.$did"),12,6);
        $mext = "grade=ccom\nshow=1\ncompany=$did\n_enc=".comConvert::sysRevert($ckey);
        $data = ['utype'=>'', 'umod'=>'company', 'atime'=>$atime, 'mext'=>$mext];
        $row = db()->table('active_login')->where("ckey='$ckey' AND uname='(set)' AND atime>".($atime-6*3600))->find();
        if(empty($row)){ // ->where("ckey='$ckey' AND uname='(set)' AND atime>".($atime-6*3600))
            $data = $data + ['ckey'=>"$ckey", 'uname'=>'(set)'];
            db()->table('active_login')->data($data)->replace();
        }
        // 
        $exmkv = req('exmkv', '0.0.0');
        $qrurl = surl('hi:login-wecdir','',1) . "?scope=snsapi_userinfo&state=set^{$ckey}^$exmkv&_v=" . time(); 
        $qrcode = PATH_BASE . "?ajax-vimg&mod=qrShow&data=" . str_replace(['?','&'],['%3F','%26'],$qrurl);
        #echo "$qrurl"; die();
        header("location:$qrcode");
        die($qrcode);
    }

    function _defAct(){
        $re = &$this->re; 
        $key = $this->ucfg['key']; //dump($key);
        $csno = empty($re['vars']['cscorp']['csno']) ? '(null)' : $re['vars']['cscorp']['csno'];
        // check-User
        $f1 = $re['vars']['uflag']=='inmem'; //dump([$csno, $key]); 
        $f2 = $re['vars']['uflag']=='company' && $csno==$key; 
        if(!($f1||$f2)){ 
            $re['vars']['errno'] = "Error-Custom-Perm";
            $re['vars']['errtip'] = "无权限操作";
            $re['vars']['errmsg'] = "无权限操作此资料:{$key} : 请重新登录！";
            $re['newtpl'] = 'home/info'; // 设置模板
            return api::v($re);
        }  
        // Check-Custom
        if(empty($re['vars']['cscorp'])){
            $re['vars']['cscorp'] = data('cscorp.join',"csno='$key'",1); //  AND `show`='all'
        }
        if(empty($re['vars']['cscorp'])){
            $re['vars']['errno'] = "Error-Custom-CODE";
            $re['vars']['errtip'] = "客户编号错误！";
            $re['vars']['errmsg'] = "错误编号{$key} : 请重新登录客户！";
            $re['newtpl'] = 'home/info'; // 设置模板
            return api::v($re);
        } //dump($cust);
        $re['vars']['whrstr'] = "rpid='{$re['vars']['cscorp']['did']}'";
        $re['newtpl'] = 'equip/mtype';
        return api::v($re);
    }

    function _detailAct(){
        $re = &$this->re;
        $key = $this->ucfg['key']; //dump($key);
        $re['vars']['row'] = $row = $this->vars;
        if(empty($row['show'])){
            $re['vars']['errtip'] = "资料编号错误！";
            $re['vars']['errmsg'] = "错误编号{$key} : 请联系业务员！";
            $re['newtpl'] = 'home/info'; // 设置模板
            return $re;
        } 
        $re['vars']['cust'] = data('cscorp.join',"did='$row[rpid]'",1);
        $re['vars']['apos'] = basElm::text2arr($row['npos']); //dump($re['vars']['apos']);
        $re['newtpl'] = 'equip/detail';
        return api::v($re);
    }

    function init($ucfg, $vars){
        $re = &$this->re;
        //global $_cbase;
        $host = empty($_SERVER["HTTP_HOST"]) ? '127.0.0.1' : $_SERVER["HTTP_HOST"]; 
        $iss = basEnv::isHttps() ? 'https' : 'http';
        $re['vars']['b0url'] = PATH_PROJ; 
        $re['vars']['bfurl'] = surl('task-apply'); //"$iss://$host".PATH_PROJ; 
        //$this->re = $re;
        return;
    }

}


/*


*/
