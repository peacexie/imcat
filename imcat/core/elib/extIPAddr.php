<?php
namespace imcat;

// 获取ip地址的地理位置信息
// $ip = new extIPAddr(); //sina,yodao,1616
// echo "\n<br>".$ip->get('182.96.199.133');

class extIPAddr{
    
    // 默认接口
    private $api = 'sina'; // sina
    private $cfile = ''; // ipSina
    
    // 配置接口
    function __construct($api=''){
        global $_cbase; 
        $defapi = empty($_cbase['ucfg']['ipapi']) ? $this->api : $_cbase['ucfg']['ipapi'];
        $this->api = $api ? $api : $defapi; 
        $this->cfile = 'ip'.ucfirst($this->api);
        $file = DIR_IMCAT.'/adpt/ipapi/'.$this->cfile.'.php';
        if(file_exists($file)){
            require $file;    
        }else{
            $this->api = 'local';
            $this->cfile = 'ipLocal';
            require DIR_IMCAT.'/adpt/ipapi/'.$this->cfile.'.php';        
        }
    }
    
    // 获取数据
    function addr($ip, $text=1){ 
        //if(empty($ip)) return '';
        //if(is_numeric($ip)) $ip = extIPAddr::long2ip($ip);
        //检查IP地址
        if(!preg_match("/\b(((?!\d\d\d)\d+|1\d\d|2[0-4]\d|25[0-5])(\b|\.)){4}/", $ip)) {
            return 'IP Error';
        }
        if(basEnv::isLocal($ip)){
            return 'LAN'; //Local Area Network
        }
        $class = "\\imcat\\$this->cfile";
        $ipa = new $class();
        if(method_exists($ipa,'getAddr')){ //检查方法...
            $addr = $ipa->getAddr($ip);
        }else{
            $addr = $this->http($ipa->url,$ipa->cset,$ip);
        }
        if($text){
            $addr = $ipa->fill($addr); //各接口分别处理
        }
        return $addr;
    }
    
    // 转化:255.255.255.255=>4294967295
    static function ip2long($ip){ 
        return sprintf("%u", ip2long($ip)); 
    }
    // 转化:4294967295=>255.255.255.255
    static function long2ip($int){ 
        return long2ip($int); 
    }
    
    // Http获取数据
    function http($url, $cset, $ip){
        global $_cbase;
        if(empty($ip)) return '';
        $addr = comHttp::doGet($url.$ip); //获取原始数据
        if(empty($addr)) return ''; 
        $addr = comConvert::autoCSet($addr,$cset,$_cbase['sys']['cset']);
        return $addr;
    }


    // ipParts
    // n : 下标-0开始, pN-前面N部分(1开始), arr-返回数组, N3
    static function ipParts($ip, $n='p3'){
        $ip = trim($ip);
        $ip = str_replace(array(' ',';',','),'',$ip);
        $arr = explode('.',$ip);
        if($n=='arr'){
            return $arr;
        }
        if($n=='N3'){
            $num3 = $arr[0]*65536 + $arr[1]*256 + $arr[2];
            return $num3;
        }
        if(is_int($n) && $n>=0 && $n<count($arr)){
            return $arr[$n];
        }
        if(substr($n,0,1)=='p'){
            $n = substr($n,1);
            $str = '';
            for($i=0;$i<$n;$i++){
                $str = (empty($s)?'':'.').$arr[$i];
            }
            return $str;
        }
        return $ip;
    }

    // local,intra,ipv6,in.Code,unknow,
    static function inArea($area='CN', $ip=''){
        $ip || $ip = comSession::getUIP();
        if(basEnv::isLocal($ip)) return 'local';
        if(strpos("($ip)",':')) return 'ipv6';
        $ip3 = self::ipParts($ip, 'arr');
        $str3 = "$ip3[0].$ip3[1].$ip3[2]";
        $file = DIR_STATIC."/media/iptabs/$area-tab.php";
        include $file;
        if(strpos("($ipsStr)",";$str3;")){
            return "in.$area";
        }
        $num3 = $ip3[0]*65536 + $ip3[1]*256 + $ip3[2];
        $tabs = explode("\n",$ipsTab);
        foreach($tabs as $tmp){
            $tma = explode('~',$tmp);
            if($tma[0]<=$num3 && $num3<=$tma[1]){
                return "in.$area";
            }
        } 
        return 'unknow';
    }
    
}
