<?php
// 获取ip地址-Ip138
class ipIp138{
	
	public $url = 'http://www.ip138.com/ips138.asp?action=2&ip='; 
	public $cset = 'gb2312';
	
	// 获取数据
    //function getAddr($ip, $text=1){}
	
	// 过滤处理
	function fill($addr){
		//江苏省盐城市  联通
		//<ul class="ul1"><li>本站主数据：江苏省盐城市  联通</li><li>参考数据一：江苏省盐城市 联通</li></ul>
		$arrText = array('<ul class="ul1"><li>','</li><li>'); 
		$addr = basElm::getVal($addr, $arrText);
		$addr = substr($addr,15) ;
		return $addr;
	}
}

