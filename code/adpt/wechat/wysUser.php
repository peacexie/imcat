<?php
(!defined('RUN_INIT')) && die('No Init');
// 用户相关操作
// 如果本系统修改,就改这个文件，不用改wmp*文件

class wysUser extends wmpUser{

    public $_db = NULL;

    function __construct($cfg=array()){
        parent::__construct($cfg);
        $this->_db = db();
    }
    
    //得到一个可用的本系统用户名
    static function fmtUserName($user=''){  
        if(is_array($user) && !empty($user['nickname'])){
            $username = $user['nickname'];
        }elseif(is_object($user) && !empty($user->nickname)){
            $username = $user->nickname;
        }else{
            $username = basKeyid::kidRand('24',8);
        }
        $username = basStr::filKey(comConvert::pinyinMain($username));
        if(strlen($username)>15) $username = substr($username,0,15);
        while(self::fmtUserCheck($username)){
            $username = substr($username,0,9).'_'.basKeyid::kidRand('24',3);
        }
        return $username;
    }
    static function fmtUserCheck($username=''){ 
        $row = db()->table('users_uacc')->where("uname='$username'")->find();
        return empty($row) ? 0 : 1;
    }

    // 设置录登录状态
    static function setLoginLogger($openid=''){ 
        $row = db()->table('users_uppt')->where("pptmod='weixin' AND pptuid='$openid'")->find();
        if($row){ //cls_message::show(" .... 完善跳转 ... 绑定了直接登录");    
            usrBase::setLogin('m',$row['uname']);
        }
        return $row['uname'];
        //未绑定,进入模版
    }
    // 设置扫描登录完成
    static function setScanLogin($scene,$openid='',$username=0){ 
        self::setLoginLogger($openid); //,'sflag'=>$username
        db()->table("wex_qrcode")->data(array('stat'=>'LoginOK','openid'=>"$openid"))->where("sid='$scene'")->update();
    }
    
    // 添加用户
    static function addUser($openid,$uname,$password,$nick='',$umod='person'){ 
        $rins = usrMember::addUser($umod,$uname,$password,$nick); //,$mtel='',$memail=''
        if(isset($rins['uid'])){ 
            usrMember::bindUser($uname,'weixin',$openid);
            $uid = $rins['uid'];
            $autocheck = $rins['check'];
            $msg = "会员注册成功，请重新注册。";
        }else{
            $uid = 0;
            $autocheck = 0;
            $msg = "会员注册失败，请重新注册。";    
        }
        return array('uid'=>$uid, 'uname'=>$uname, 'password'=>$password,'autocheck'=>$autocheck, 'msg'=>$msg);
    }
    
    //绑定用户
    static function bindUser($openid,$uname,$password){  
        if(empty($uname)) die('错误:'.__FUNCTION__); //原则上没有这个情况
        $ubase = db()->table('users_uacc')->where("uname='$uname'")->find();
        $umod = $ubase['umods']; $dbpass = $ubase['upass'];
        $upass = comConvert::sysPass($uname,$password,$umod);
        if($dbpass!=$upass){  
           $uid = 0;
           $msg = '密码错误'; 
           $res = 0;
        }else{
            usrMember::bindUser($uname,'weixin',$openid);
            $uid = $ubase['uid'];
            $msg = '绑定成功';
            $res = 1;
            usrBase::setLogin('m',$uname);
        }
        return array('uid'=>$uid, 'res'=>$res, 'msg'=>$msg);
    }
    
    // 扫描过来 : setPwd
    static function resetPwd($openid,$scene,$uname){ 
        if(empty($openid)) die('错误:'.__FUNCTION__); //原则上没有这个情况
        $db = db();
        $uppt = $db->table('users_uppt')->where("pptmod='weixin' AND pptuid='$openid'")->find();
        if(empty($uppt)) return "重置失败，未绑定帐号(a)！";
        $ubase = $db->table('users_uacc')->where("uname='{$uppt['uname']}'")->find();
        if(empty($ubase)) return "重置失败，未绑定帐号(b)！";
        $password = basKeyid::kidRand('24',8);
        $upass = comConvert::sysPass($uname,$password,$ubase['umods']);
        $db->table('users_uacc')->data(array('upass'=>$password))->where("uname='{$uppt['uname']}'")->update();
        $db->table('wex_qrcode')->data(array('atime'=>0))->where("sid='{$scene}'")->update();
        $msg = "您的登录帐号为：{$uname}。<br>";
        $msg .= "您的密码重置为：{$password}。";
        return $msg;
    }

}
