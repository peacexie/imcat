<?php
/*
start_sdict-pub.htm
*/ 
class startCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
    }

    // dbtab
    function dbtabAct(){
        return array('newtpl'=>$this->ucfg['tplname']);
        // 不用使用-mob后缀
    }

}
