<?php
// comSession
class comSession { 

    //public $guid = '';
    static $guid_ck = array();

    static function set($id,$val){ 
        $_SESSION[self::fill($id)] = self::fill($val);
    }
    static function get($id){ 
        return isset($_SESSION[self::fill($id)]) ? $_SESSION[self::fill($id)] : '';
    }
    // Session过滤
    static function fill($xStr){
        $xStr = str_replace(array('"',"'","\\",'|',':',';'),'',$xStr); //'<','>',
        return $xStr;
    }
    /*
    [ip] => 192.168.1.11 / sdltkfc6tdq8ijlui7lc68brh1
    [fix] => sessid@safil-.bbmU-dcxyE-XwrmC-ZDgoQ-pLTC+
    [enc] => 3bcf0114e73f64a55240b7c55fea7069
    [ua] => Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36
    */
    static function guid($flg='',$nsp='',$mode=0){ //mode:Cook,Sess,UIP
        global $_cbase; 
        $sipck = empty($mode) ? $_cbase['ucfg']['guid'] : $mode;
        if(empty($sipck) || !in_array($sipck,array('Sess','UIP','Cook'))) $sipck = 'Cook'; 
        $func = "get$sipck";
        $sipck = self::$func($nsp);
        $ua = $_cbase['run']['userag']; 
        $fix = empty($nsp) ? (@$_cbase['safe']['site']) : $nsp; 
        strstr($flg,'time') && $fix .= '@'.$_cbase['run']['timer'];
        strstr($flg,'safil') && $fix .= '@'.$_cbase['safe']['safil'];
        $enc = md5($sipck.$fix.$ua); //,'fix'=>$fix
        return array('sip'=>$sipck,'sua'=>$ua,'sid'=>$enc);
    }
    
    // cookie
    static function getCook($nsp=''){
        global $_cbase;
        if(!empty(self::$guid_ck[$nsp])) return self::$guid_ck[$nsp];
        $ua = md5($_cbase['run']['userag'].$nsp);
        $ckey = "{$nsp}_".substr($ua,0,12);
        $cval = comCookie::oget($ckey);
        if(strlen($cval)<32){
            $cval = basKeyid::kidTemp().'-'.basKeyid::kidRand('24',9).'-'.substr($ua,9,9);
            comCookie::oset($ckey,$cval,31622400); //86400*366 = 31 622 400
        }
        self::$guid_ck[$nsp] = $cval;
        return self::$guid_ck[$nsp];
    }
    
    // session_id
    static function getSess($nsp=''){
        return session_id();
    }

    // 获取客户端真实IP(用于guid,session判断)
    static function getUIP($nsp=''){    
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $list = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = $_SERVER['REMOTE_ADDR'] = $list[0];
        }else{ //$_SERVER['HTTP_CLIENT_IP'] 是否判断?
            $ip = $_SERVER['REMOTE_ADDR'];
        } //修正...??? 
        return $ip;
    }

}
