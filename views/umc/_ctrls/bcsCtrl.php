<?php
namespace imcat\umc;

use imcat\basEnv;
use imcat\basElm;
use imcat\basKeyid;
use imcat\basMsg;
use imcat\comConvert;
use imcat\basOut;
use imcat\basReq;
use imcat\extWework;
use imcat\glbDBExt;
use imcat\vopShow;
use imcat\usrMember;

use imcat\vopApi as api;
use imcat\hi\uioCtrl;

/*
*/

class bcsCtrl extends uioCtrl{

    public $gtab = [
        'gdoing'    => ['处理中', 'apnew','assign','redo','servchk','aptime','ushift','served'],
        'gdone'     => ['待评价', 'done','paied','score'],
        'gclose'    => ['已关闭', 'close'],
        'gwait'     => ['等待中', 'susing','suspend','eqfix','eqfac','attapply','attbuy'],
        /*
        'apnew'    => ['新工单','apnew'],
        'assign'   => ['工单派工','assign','redo'],
        'servchk'  => ['接收预约','servchk','aptime','ushift'],
        'served'   => ['服务打卡','served'],
        'done'     => ['完成待评价','done','paied','score'],
        'close'    => ['工单关闭','close'],
        'susing'   => ['挂单返修','susing','suspend','eqfix','eqfac'],
        'attapply' => ['配件申请','attapply','attbuy'],
        //'eqfix'    => ['设备维修','eqfix','eqfac'],
        */
    ];
    
    public $urows = [];
    public $urtab = [
        '(system)' => ['uname'=>'(system)', 'mname'=>'(系统)',   'mpic'=>'',],
        '(scaner)' => ['uname'=>'(scaner)', 'mname'=>'(扫码者)', 'mpic'=>'',],
    ];

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){
        parent::__construct($ucfg, $vars);
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->initBcs($ucfg, $vars);
    }

    // bind-check:绑定检查
    function bindCheck(&$row){ 
        $this->bindCheckBase($row); 
        $re = &$this->re; //dump($this->re['vars']); 
        /*
        if(!empty($re['vars']['uinfo'])){
            $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod']; 
            $uimod = $re['vars']['uimod']; $uname = empty($uimod['uname']) ? $uinfo['uname'] : $uimod['uname'];
            if($umod=='adminer'){ $this->re['vars']['ucdebug'] .= ",{$uname},"; } 
        } //dump($this->re['vars']); 
        */
        if(smod('cscorp') && !empty($this->re['vars']['uimod']['company'])){
            $this->re['vars']['cscorp'] = data('cscorp.join',"did='{$this->re['vars']['uimod']['company']}'",1); 
        }
        return;
    }

    function initBcs($ucfg, $vars){
        if($this->mod=='bcs'){ die('Error-Url(initBcs)!'); }
        $re = &$this->re;
        if(empty($re['vars']['uinfo'])){
            $skips = ['task-print'];
            if(in_array($this->ucfg['mkv'],$skips)){ return; }
            $vars = ['errno'=>'NOT-Login', 'errmsg'=>'未登录']; 
            $jpmkv = req('jpmkv',"umc:{$this->ucfg['mkv']}");
            $sec = req('sec','full');
            $url = surl('hi:login-setdf')."?jpmkv=$jpmkv&domkv=login?sec=$sec&_r=".date('mdHi');
            //  {surl(hi:login-setdf)}?jpmkv=comm:{=$this->mkv}&domkv=login-weedu&sec=full
            return api::v($vars, 'dir', $url);
        }
        $re['vars']['wecfgs'] = extWework::wecfgs();
    }

    function urow($uname, $udoc=[]){
        if(isset($this->urtab[$uname])){ // 系统用户
            return $this->urtab[$uname];
        }elseif(isset($this->urows[$uname])){ // 缓存
            return $this->urows[$uname];
        }elseif(isset($this->re['vars']['utab'][$uname])){ // 企业员工
            $utmp = extWework::getUser($uname, 'AppCS'); 
            $urow = ['uname'=>$uname, 'mname'=>$utmp['name'], 'mpic'=>$utmp['avatar']];
        }else{ // 会员
            $row = ['uname'=>$uname];
            $urow = $this->getUser($row, 'idpwd', $exacc=0);
        }
        $this->urtab[$uname] = $urow; 
        return $urow;
    }

}

/*


*/
