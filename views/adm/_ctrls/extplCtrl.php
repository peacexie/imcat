<?php
namespace imcat\adm;

/*
    按一般模板解析
*/ 
class extplCtrl extends extraCtrl{

    public $ucfg = array();
    public $vars = array();

    function __construct($ucfg=array(), $vars=array()){ 
        parent::__construct($ucfg, $vars); 
    }

    function _defAct(){
        #return array('tplnull'=>1);
    }
    
}
