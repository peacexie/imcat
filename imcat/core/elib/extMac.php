<?php
namespace imcat;

// extMacMAC地址
// 目前支持WIN/LINUX系统 获取机器网卡的物理(MAC)地址
//  $mac = new extMac(); echo $mac->get();

class extMac{

    var $res = array(); // 返回带有MAC地址的字串数组 
    var $macAddr = ''; 

    function get($os=''){
        if(!function_exists('exec')){
            return '(exec_not_exists)';
        }
        //$os || $os = PHP_OS;
        if(IS_WIN){ 
            $this->forWindows();
        }else{
            $this->forLinux();
        }
        $temp = array(); 
        foreach($this->res as $value){ 
            if(preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i", $value, $temp)){ 
                $this->macAddr = $temp[0]; 
                break; 
            } 
        }
        unset($temp); 
        return $this->macAddr; 
    } 

    function forWindows(){ 
        @exec("ipconfig /all", $this->res); 
        if($this->res) 
            return $this->res; 
        else{ 
            $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe"; 
            if(is_file($ipconfig)) 
                @exec($ipconfig." /all", $this->res); 
            else 
                @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->res); 
            return $this->res; 
        } 
    } 

    function forLinux(){ 
        @exec("ifconfig -a", $this->res); 
        return $this->res; 
    } 

}
