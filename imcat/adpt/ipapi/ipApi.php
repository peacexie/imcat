<?php
namespace imcat;
// 获取ip地址-ipApi
class ipApi{
    
    public $url = 'http://ip-api.com/json/'; 
    public $cset = 'utf-8';
    
    // 获取数据
    function getAddr($ip){
        $addr = comHttp::doGet($this->url.$ip.'?lang=zh-CN');
        return $addr;
    }
    
    // 过滤处理
    function fill($addr){
        //{"as":"AS134...","city":"Guangzhou","country":"China","countryCode":"CN","isp":"China Telecom Guangdong","lat":23.1167,"lon":113.25,"org":"China...","query":"61.145.169.200","region":"44","regionName":"Guangdong","status":"success","timezone":"Asia/Shanghai","zip":""}
        $arr = json_decode($addr,1);
        $arr = array_filter($arr); //dump($arr);
        if(empty($arr)){
            $addr = '-';
        }elseif($arr['status']=='fail'){
            $addr = 'Error:'.$arr['message'];
        }else{
            unset($arr['as'],$arr['query'],$arr['status'],$arr['org'],$arr['zip']);
            $addr = implode(',',$arr);
        }
        return $addr; // 河北,中国,CN,China Telecom hebei,39.8897,115.275,13,河北省,Asia/Shanghai
    }
}

