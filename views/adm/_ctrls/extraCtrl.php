<?php
namespace imcat\adm;

/*
    扩展基础
*/ 
class extraCtrl{

    public $ucfg = array();
    public $vars = array();

    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->chkLogin();
    }

    // 
    function chkLogin(){
        $user = \imcat\usrBase::userObj('Admin');
        if($user->userFlag=='Guest'){
            header('Location:'."?login");
        }
    }

}
