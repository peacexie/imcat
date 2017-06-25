<?php
include dirname(__FILE__).'/_admCtrl.php';
/*
*/ 
class bincCtrl extends _admCtrl{
	
    private $skacts = array('exd_inc1','act_ops','_pub_cfgs');

    function __construct($ucfg=array(),$vars=array()){ 
        parent::__construct($ucfg,$vars);
        $key = $this->ucfg['key'];
        if(in_array($key,$this->skacts)){
            dump("Error:$key!");
            die();
        }
    }

    //function _emptyAct(){}
    
}
