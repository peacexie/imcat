<?php
// 获取ip地址-Sina
class ipSina{
    
    public $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='; 
    public $cset = 'utf-8';
    
    // 获取数据
    //function getAddr($ip){}
    
    // 过滤处理
    function fill($addr){
        //{"ret":1,"start":-1,"end":-1,"country":"\u4e2d\u56fd","province":"\u5e7f\u4e1c","city":"\u4e1c\u839e","district":"","isp":"","type":"","desc":""}
        $arr = json_decode($addr,1);
        $arr = array_filter($arr); //dump($arr);
        if(empty($arr)){
            $addr = '-';
        }else{
            unset($arr['ret'],$arr['start'],$arr['end'],$arr['type'],$arr['desc']);
            $addr = implode(',',$arr);
        }
        return $addr; // 中国,辽宁,营口
    }
}

