<?php
namespace imcat;

/**
safBase : 基础-安全过滤(Safil=Safety Filter)
  Stop,ipStop,robotStop,
  RndA,
*/
class safBase{
    
    // ---- 用户信息 基础过滤项 --------------------------------------- 
    
    // IPStop
    static function ipStop($ip=''){
        $ip || $ip = self::userIP();
        $tab = glbConfig::read('ipstop','sy');
        if(empty($ip) || empty($tab)) return;
        if($tab){
            if(preg_match("/^($tab)$/",$ip)) self::Stop('ipStop');
        }
    }
    
    // RobotStop
    static function robotStop(){
        if(basEnv::isRobot()) self::Stop('robotStop');
    }
    
    // ---- 公共杂项方法 ---------------------------------------
    
    // Stop - 停止运行并记录操作
    static function Stop($key,$exmsg=''){
        if($key=='robotStop') glbHtml::httpStatus(403); 
        $cfg = array(
            'robotStop'=>'HTTP 403 Forbidden',
            'ipStop'=>'Stop by IP Fobidden!',
            'urlFrom'=>'Stop by HTTP_REFERER!',
            'urlScan'=>'Stop by Safety Scan!',
        );
        $msg = isset($cfg[$key]) ? $cfg[$key] : $key;
        $exmsg && $msg .= " [$exmsg]";
        glbError::show($msg);
    }
    
    // RndA - 由时间戳, 生成随机特殊字符数组对
    /* Return : Array(
        [0^1>2+] => '',
        [3-] => '85d6',
        [4&] => '9637',
        [5~] => '973f',);*/
    static function RndA($timer,$encode){
        global $_cbase;
        $safe = $_cbase['safe'];
        $stim = str_replace('.','',$timer);
        $stim = strrev($stim); $a = array(); 
        $rnum = $safe['rnum'];
        $rspe = $safe['rspe'];  
        $spla = strlen($rspe);
        for($i=0;$i<strlen($stim);$i++){
            $t = substr($stim,$i,1)+substr($rnum,$i,1);
            $a[] = $t; 
            if($i<$spla){
                $t2 = $t % $spla;
                $c = substr($rspe,$t2,1);
                $rspe = (($t2 % 2)==1 ? $c : '').str_replace($c,'',$rspe).(($t2 % 2)==0 ? $c : '');
            }
        }
        $r = array(); $k = '';
        $m = 1+($a[2] % 4);
        $len = count($a) - ($a[3] % 9);
        for($i=0;$i<$len;$i++){
            if($i<$m){
                $k .= $i.substr($rspe,$i,1);
            }elseif($i==$m){
                $r[$k.$i.substr($rspe,$i,1)] = '';
            }else{
                $r[$i.substr($rspe,$i,1)] = $a[$i].substr($encode,$i*2,3); 
            }
        }
        return $r;
    }

    // --- End ----------------------------------------
    
}
