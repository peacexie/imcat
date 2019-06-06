<?php
namespace imcat\doc;

use imcat\glbHtml;
use imcat\usrBase;

/*
公共模板扩展函数
*/ 
class texBase extends \imcat\dev\texBase{
    
    static $data = array();
    //protected $xxx = array();

    static function init($obj){
        global $_cbase;
        $_cbase['sys_name'] = 'imcat(贴心猫)';
        if(!empty($_cbase['login_dev'])){
            $user = usrBase::userObj(); $msg = '';
            if(empty($user)){
                $msg = 'Need '.($_cbase['login_dev']=='adminer' ? 'adminer' : '').' Login!';
            }elseif($_cbase['login_dev']=='adminer' && $user->userType!='adminer'){
                $msg = 'Need adminer Login!';
            }
            if($msg){
                glbHtml::page('Need Login!');
                glbHtml::page('body');
                echo "\n<p>$msg</p>\n";
                glbHtml::page('end');
            }
        }else{
            $user = NULL;
        }
    }

}
