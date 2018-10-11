<?php
namespace imcat\base;

//use imcat\vopTpls;

class _defCtrl{

    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
    }

    // _defAct
    function _defAct(){
        global $_cbase;
        $tpl = $this->ucfg['tplname'];
        $pres = array('info/', 'user');
        if(!in_array(substr($tpl,0,5),$pres)){
            die(__FUNCTION__);
            new \imcat\exvJump();
        }
    }

}
