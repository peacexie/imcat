<?php
namespace imcat\base;

//use imcat\vopTpls;

class ajaxCtrl{

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
        $fp = "/{$_cbase['tpl']['vdir']}/$tpl.php"; 
        if(file_exists(DIR_VIEWS.$fp)){
            include(DIR_VIEWS.$fp);
        }
        die();
    }

}
