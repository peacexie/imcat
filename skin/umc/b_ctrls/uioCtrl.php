<?php
/*
*/ 
class uioCtrl{
    
    public $ucfg = array();
    public $vars = array();
    public $newtpl = '';

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->_init();
    }

    function _init(){
        $tplname = $this->ucfg['tplname'];
        if(basEnv::isMobile()){
            $this->newtpl = $tplname.'-mob';
        }
    }

    function loginAct(){
        if($this->newtpl) return array('newtpl'=>$this->newtpl);
    }
    /*function logoutAct(){
        $user = usrBase::userObj('Member'); 
        $user->logout();
        header('Location:?user');
    }*/
    function applyAct(){
        if($this->newtpl) return array('newtpl'=>$this->newtpl);
    }

}
