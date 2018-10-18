<?php
namespace imcat\base;

use imcat\vopTpls;
use imcat\basMsg;
//use imcat\basDebug;

class fileCtrl{

    public $ucfg = array();
    public $vars = array();
    public $user = null;

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        // check-perm
        $this->user = user(array('Admin','Member'));
        $this->user->userFlag=='Guest' && basMsg::show('Not Login.','die'); //未登录
    }

    function updeelAct(){
        global $_cbase;
        $user = $this->user;
        $allpars = tex('texFile')->fopPars(0);
        include vopTpls::tinc('info/file-updeel',0);
        die();
    }

    // _defAct
    function _defAct(){
        global $_cbase;
        $tpl = $this->ucfg['tplname'];
        $tpl = str_replace('file/', 'info/file-', $tpl);
        return array('newtpl'=>$tpl); // file/upone -=> info/file-upbat
    }

}
