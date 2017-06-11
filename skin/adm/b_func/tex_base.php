<?php
/*
公共模板扩展函数
*/ 
class tex_base{
    
    //protected $xxx = array();
    
    static function base1($show,$a=''){ 
        echo "<br>base1::";
    }
    
    static function init($obj){
        $user = usrBase::userObj('Admin');
        if(!in_array($obj->key,array('login','help'))){
            if($user->userFlag=='Guest') header('Location:'."?login");
            $user->setSess();
        }
        return $user;    
    }
    
    static function pend(){

    }

}
