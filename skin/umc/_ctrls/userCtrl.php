<?php
namespace imcat\umc;

/*
*/ 
class userCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
    }

    /*function homeAct(){
        header('Location:'."?user");
    }*/

}
