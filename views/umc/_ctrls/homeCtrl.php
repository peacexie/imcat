<?php
namespace imcat\umc;

use imcat\basEnv;
use imcat\basElm;
use imcat\basOut;
use imcat\basReq;
use imcat\extWework;
use imcat\glbDBExt;
use imcat\usrMember;

/*
*/
class homeCtrl extends bcsCtrl{

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        parent::__construct($ucfg, $vars);
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->init($ucfg, $vars);
    }

    function readAct(){
        //
    }

    function homeAct(){
        $re = &$this->re;
        #
        return $re;
    } 

    function _defAct(){
        $re = &$this->re;
        return $re;
    } 

    function init($ucfg, $vars){
        $re = &$this->re; //dump($re);
        $wecfg = read('wework', 'ex');
        if(!empty($wecfg['isOpen'])){
            if(!empty($re['vars']['uinfo'])){
                $uinfo = $re['vars']['uinfo']; $umod = $uinfo['umod']; 
                $uimod = $re['vars']['uimod']; $uname = empty($uimod['uname']) ? $uinfo['uname'] : $uimod['uname'];
                if($umod=='adminer'){ $this->re['vars']['udebug'] .= ",{$uname},"; } 
            }
            $re['vars']['whrstr'] = $whrstr = texBase::sqlType($re); 
            $list = data('cstask', $whrstr, '5');
            texBase::convData($list);
            $re['vars']['list'] = $list;
            $this->re = $re;
        }elseif(smod('corder')){
            header('Location:'.surl('order'));
        }else{
            //
        }
    
    }


}
