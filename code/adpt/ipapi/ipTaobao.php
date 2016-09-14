<?php
// 获取ip地址-Taobao
class ipTaobao{
	
	public $url = 'http://ip.taobao.com/service/getIpInfo.php?ip='; 
	public $cset = 'utf-8';
	//read:        http://ip.taobao.com/
	
	// 获取数据
    //function getAddr($ip, $text=1){}
	
	// 过滤处理
	function fill($addr){
		//中国,CN,华东,300000,江苏省,320000,盐城市,320900,,-1,联通,100026,122.96.199.133 
		//{"code":0,"data":{"country":"\u4e2d\u56fd","country_id":"CN","area":"\u534e\u4e1c","area_id":"300000","region":"\u6c5f\u82cf\u7701","region_id":"320000","city":"\u76d0\u57ce\u5e02","city_id":"320900","county":"","county_id":"-1","isp":"\u8054\u901a","isp_id":"100026","ip":"122.96.199.133"}}
		$arr = json_decode($addr,1);
		$addr = implode(',',empty($arr['data']) ? '-' : $arr['data']);
		$addr = preg_replace("/\d{1,3}([.][0-9]{1,3}){3,15}/",'',$addr);
		return $addr;
	}
}

