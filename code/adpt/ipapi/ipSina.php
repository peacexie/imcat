<?php
// 获取ip地址-Sina
class ipSina{
    
    public $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip='; 
    public $cset = 'gb2312';
    
    // 获取数据
    //function getAddr($ip){}
    
    // 过滤处理
    function fill($addr){
        //1    14.216.0.0        14.222.255.255    中国    广东    东莞        电信
        //1    152.72.131.0    152.72.245.255    美国    威斯康星州    Racine
        $addr = preg_replace("/\d{1,3}([^\n]+)\d{1,3}/",'',$addr);
        $addr = trim($addr);
        return $addr;
    }
}

