<?php
namespace imcat;

// 阳光公采API

class extGbuy{

    static $enkey = '2~B`y^';
    
    function __construct() {
        //$;
    }

    // 获取 access_token
    static function getActoken($data, $reval=0) {
        global $_cbase; 
        $user = empty($_cbase['gbuy']['user']) ? '' : $_cbase['gbuy']['user'];
        $pass = empty($_cbase['gbuy']['pass']) ? '' : $_cbase['gbuy']['pass'];
        // 
        $timestamp = empty($data['timestamp']) ? '' : $data['timestamp'];
        $username  = empty($data['username'])  ? '' : $data['username'];
        $password  = empty($data['password'])  ? '' : $data['password'];
        $sign      = empty($data['sign'])      ? '' : $data['sign'];
        // post={"username":"zhuoke","password":"ds...rN","timestamp":"2020-08-31 13:02:31","sign":"92...d8"};
        if(empty($timestamp)||empty($username)||empty($password)||empty($sign)){
            $res['success'] = false;
            $res['desc'] = 'Lack of parameters';
            return vopApi::view($res);
        }
        // sign=username+password+timestamp+password
        $csign = md5($username.$password.$timestamp.$password);
        if($csign!=$sign||$user!=$username||$pass!=$password){
            $res['success'] = false;
            $res['desc'] = 'Error sign or user password';
            return vopApi::view($res);
        }
        // 
        $stamp = time();
        $token = "$user@$stamp@$pass";
        $token = comConvert::sysRevert($token, 0, self::$enkey); 
        $res['access_token'] = $token;
        $res['success'] = true;
        $res['expires_at'] = date('Y-m-d H:i:s', $stamp+12*3600); // 12h
        if($reval){
            return $res;
        }
        return vopApi::view($res);
    }
    // check access_token
    static function checkActoken($token){
        global $_cbase; 
        $user = empty($_cbase['gbuy']['user']) ? '' : $_cbase['gbuy']['user'];
        $pass = empty($_cbase['gbuy']['pass']) ? '' : $_cbase['gbuy']['pass'];
        $stamp = time();
        // 
        $unstr = comConvert::sysRevert($token, 1, self::$enkey);
        if(empty($unstr)){
            $res['success'] = false;
            $res['desc'] = 'Empty token';
            return vopApi::view($res);
        }
        $unarr = explode('@', "$unstr@@@@");
        if(empty($unarr[0])||empty($unarr[1])||empty($unarr[2])||$user!=$unarr[0]||$pass!=$unarr[2]){
            $res['success'] = false;
            $res['desc'] = 'Error token';
            return vopApi::view($res);
        }
        $unarr[1] = is_int($unarr[1]) ? $unarr[1] : '0';
        if($stamp-$unarr[1]>18*3600){
            $res['success'] = false;
            $res['desc'] = 'token_expired';
            return vopApi::view($res);
        }
    }

}
