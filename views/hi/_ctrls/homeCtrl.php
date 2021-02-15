<?php
namespace imcat\hi;

use imcat\basEnv;
use imcat\basElm;
use imcat\basOut;
use imcat\basReq;
use imcat\extWework;
use imcat\glbDBExt;

/*
*/ 
class homeCtrl{
    
    public $ucfg = array();
    public $vars = array();

    public $re = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->init();
    }

    function homeAct(){
        header('Location:'.surl('login'));
        die(); //PATH_PROJ.'/'
        $re['vars'] = [];
        return $re;
    } 

    function _defAct(){
        $re['vars'] = $this->vars;
        return $re;
    } 

    function init(){
        global $_cbase;
        //
    }

}
