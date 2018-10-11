<?php
namespace imcat;

/* 
 */

class comToken{

    static $rate = 3; // 单位:分钟
    //static $ratw = 5; // 单位:秒(数据写入更新)

    /*public function __construct($kid='',$token=''){
        //
    }*/

    // 得到一个唯一token
    static function guid($kid=''){
        if(empty($kid)) return '';
        $p1 = comConvert::sysEncode(microtime(1).$kid,'rest-token',23);
        $p2 = comConvert::sysBase64($kid);
        return "$p1.$p2";
    }

    // 检测:rest权限,
    static function perm($token,$mod,$key){
        $db = glbDBObj::dbObj(); 
        // 权限:
        $row = $db->table('token_rest')->where("token='$token'")->find();
        if($row){
            if($row['exp']<$_SERVER["REQUEST_TIME"]){ // 过期
                glbError::show("Token Expired [".(date('Y-m-d H:i:s',$row['exp']))."]");
            } // demo=table,list,check;
            $perm = basElm::text2arr(str_replace(';','&',$row['perm']));
            if(!isset($perm[$mod])){
                glbError::show("No Permission for [$mod]");
            }elseif(!strstr($perm[$mod],$key)){
                glbError::show("No Permission [$key] in [$mod]");
            }else{
                $pmod = $perm[$mod]; // 用于返回
            }
            $row['perm'] = $perm;
            $row['pmod'] = $pmod;
        }else{ 
            glbError::show("Token Error [$token]"); 
        }
        return $row;
    }
    // 检测:limit频率
    static function limit($token,$mod,$key){
        $db = glbDBObj::dbObj(); 
        // 频率:
        $arr = array('kid'=>$token,'mod'=>$mod,'act'=>$key);
        $rli = $db->table('token_limit')->where($arr)->find();
        if($rli){ 
            $gap = $_SERVER["REQUEST_TIME"]-$rli['etime'];
            $rate = empty($row['rate']) ? self::$rate : intval($row['rate']);
            if($gap<$rate*60){
                $wait = $rate*60 - $gap;
                glbError::show("Too many Request! Please Wait $wait(s)");
            }
        }else{ 
            $gap = $_SERVER["REQUEST_TIME"]-1;
            $db->table('token_limit')->data($arr)->insert(0); 
        }
        return $gap;
    }

    // 更新:limit
    static function upd($token,$mod,$key){
        $db = glbDBObj::dbObj();
        $arr = array('kid'=>$token,'mod'=>$mod,'act'=>$key); 
        $db->table('token_limit')->data(array('etime'=>$_SERVER["REQUEST_TIME"]))->where($arr)->update(0);
    }

}
