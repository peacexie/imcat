<?php
namespace imcat;

// usrExtra
class usrExtra{    
    
    //public $sessid = '';
    
    function __construct() {
        //parent::__construct(''); 
    }


    //得到一个可用的本系统用户名
    static function fmtUserName($user=''){  
        if(is_array($user) && !empty($user['nickname'])){
            $uname = $user['nickname'];
        }elseif(is_object($user) && !empty($user->nickname)){
            $uname = $user->nickname;
        }else{
            $uname = $user ? $user : basKeyid::kidRand('24',8);
        }
        $uname = basStr::filKey(comConvert::pinyinMain($uname));
        if(strlen($uname)>15) $uname = substr($uname,0,15);
        while(self::fmtUserCheck($uname)){
            $uname = substr($uname,0,9).'_'.basKeyid::kidRand('24',3);
        }
        return $uname;
    }
    static function fmtUserCheck($uname=''){ 
        $row = db()->table('users_uacc')->where("uname='$uname'")->find();
        return empty($row) ? 0 : 1;
    }

    // 设置录登录状态
    static function setLoginLogger($openid='',$pptmod='weixin'){ 
        $row = self::getUserOpenid($openid,$pptmod);
        if($row){    
            usrBase::setLogin('m',$row['uname']);
        }
        return $row['uname'];
    }
    
    //由openid得到一条用户数据
    static function getUserOpenid($openid='',$pptmod='weixin',$exinfo=0){
        $row = db()->table('users_uppt')->where("pptuid='$openid' AND pptmod='$pptmod'")->find();
        if($exinfo){
            $uacc = db()->table('users_uacc')->where("uname='{$row['uname']}'")->find();
            $row += $uacc;
        }
        return $row;
    }

}
