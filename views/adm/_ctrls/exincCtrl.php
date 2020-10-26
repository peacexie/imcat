<?php
namespace imcat\adm;

/*
    直接include`.php`文件
*/ 
class exincCtrl extends extraCtrl{

    function __construct($ucfg=array(), $vars=array()){ 
        parent::__construct($ucfg, $vars); 
    }

    function _defAct(){
        include dirname(__DIR__).'/exinc/'.$this->ucfg['key'].'.php';
        return array('tplnull'=>1);
    }
    
}
