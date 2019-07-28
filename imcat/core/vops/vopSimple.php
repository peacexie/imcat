<?php
namespace imcat;

class vopSimple extends vopShow{
 
    public $dir = '';
    public $tplfull = '';
    public $da = ''; // def-act

    //function __destory(){  }
    function __construct($dir, $da='home', $view=1){
        $this->dir = $dir; // job6
        $this->da = $da;
        $this->tpl();
        if($view) $this->view();  
    }
   
    function tpl(){
        global $_cbase;
        $_cbase['tpl']['vbase'] = dirname($this->dir);
        $_cbase['tpl']['vdir'] = basename($this->dir);
        $this->tplCfg = $_cbase['tpl'];
        $act = empty($_SERVER['QUERY_STRING']) ? $this->da : $_SERVER['QUERY_STRING'];
        $tpl = "tpls/{$act}";
        $_cbase['run']['tplname'] = $tpl;
        $this->tplfull = vopComp::main($tpl);
    }

    function view($data=array()){
        global $_cbase;
        include $this->tplfull;
    }

}
