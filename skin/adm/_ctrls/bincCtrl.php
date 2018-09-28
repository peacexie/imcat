<?php
namespace imcat\adm;

include dirname(__FILE__).'/_defCtrl.php';
/*
*/ 
class bincCtrl extends _defCtrl{
	
    public $skacts = array('exd_inc1','act_ops','_pub_cfgs');

    function __construct($ucfg=array(), $vars=array()){ 
        parent::__construct($ucfg, $vars);
        $key = $this->ucfg['key'];
        if(in_array($key,$this->skacts)){
            dump("Error:$key!");
            die();
        }
    }

    //function _defAct(){}
    
}
