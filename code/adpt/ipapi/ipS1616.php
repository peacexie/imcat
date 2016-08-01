<?php
// 获取ip地址-S1616
class ipS1616{
	
	public $url = 'http://chaxun.1616.net/s.php?type=ip&output=json&callback=data&v='; 
	public $cset = 'utf-8';
	
	// 获取数据
    //function getAddr($ip, $text=1){}
	
	// 过滤处理
	function fill($addr){
		//江苏省盐城市 联通 
		//data({"Ip":"122.96.199.133","Isp":"江苏省盐城市 联通","Browser":"","OS":"Windows 7","QueryResult":1}) 
		$arrText = array('Isp":"','","Browser');
		$addr = basElm::getVal($addr, $arrText);
		return $addr;
	}
}

