<?php
namespace imcat\base;

use imcat\exvJump;
use imcat\glbHtml;

class _defCtrl{

    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->_defAct();
    }

    // _defAct
    function _defAct(){
        global $_cbase; 
        $tpl = $this->ucfg['tplname'];
        $pres = array('info/', 'user/'); # info/map-baidu
        if(!in_array(substr($tpl,0,5),$pres)){
            $res = new exvJump();
            //glbHtml::httpStatus(404);
            die('Error `mkv`! @ '.__FUNCTION__);
        }
    }

}
