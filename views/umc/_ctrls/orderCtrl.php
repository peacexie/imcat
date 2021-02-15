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

class orderCtrl{

    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->initOrder($ucfg, $vars);
    }

    function _defAct(){
        $re = &$this->re;
        $re['vars']['key'] = $key = $this->ucfg['key'];
        $uname = $re['vars']['uname'];
        $whrstr = "auser='$uname'"; // 
        if(empty($key)){
            $stitle = lang('user.pub_hisord');
        }elseif($key=='nodone'){ 
            $stitle = lang('user.ord_noend');
            $whrstr .= " AND ordstat='new'";
        }elseif($key=='isdone'){
            $stitle = lang('user.ord_isend');
            $whrstr .= " AND ordstat='done'";
        }
        $re['vars']['whrstr'] = $whrstr; //dump($whrstr);
        $re['vars']['stitle'] = $stitle;
        $re['newtpl'] = 'order/lists';
        return api::v($re);
    }

    function homeAct(){
        return $this->_defAct();
    }

    function initOrder($ucfg, $vars){
        $uio = new uioCtrl($ucfg, $vars);
        $re = $uio->re;
        $this->re = $re; //dump($re);
        //return api::v($re);
    }

}

/*


*/
