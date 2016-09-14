<?php
/*
 * 算法代码：
http://www.fuziba.com/2012/08/01/php%E8%AF%BB%E5%8F%96%E7%BA%AF%E7%9C%9Fip%E6%95%B0%E6%8D%AE%E5%BA%93/
 * 地址库：
http://pc6.com/softview/SoftView_41490.html (纯真IP数据库)
下载安装，把qqwry.dat提取出来放到[/ilibs/ip_qqwry.dat]；最后卸载...
为保持本代码的瘦小，默认没有这个数据库文件，2014年6月15日对应的大小为9M，为本程序的几倍！
*/
// 获取ip地址
class ipLocal{
	
	public $url = 'local'; 
	public $cset = 'gb2312'; //bin,gb2312
	
	// 获取数据
    function getAddr($ip){
		//IP数据文件路径
		$dat_path = DIR_STATIC.'/ilibs/ip_qqwry.dat';
		//打开IP数据文件
		if(!$fd = @fopen($dat_path, 'rb')){
			return 'ip_qqwry.dat  Error';
		}
		
		//分解IP进行运算，得出整形数
		$ipNum = extIPAddr::ip2long($ip); 
		//获取IP数据索引开始和结束位置
		$DataBegin = fread($fd, 4);
		$DataEnd = fread($fd, 4);
		$ipbegin = implode('', unpack('L', $DataBegin));
		if($ipbegin < 0) $ipbegin += pow(2, 32);
		$ipend = implode('', unpack('L', $DataEnd));
		if($ipend < 0) $ipend += pow(2, 32);
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;
	 
		$BeginNum = 0;
		$EndNum = $ipAllNum;
		$ip1num = $ip2num = 0; //Peace修正Notic错误
		$ipAddr1 = $ipAddr2 = ''; //Peace修正Notic错误
	 
		//使用二分查找法从索引记录中搜索匹配的IP记录
		while($ip1num>$ipNum || $ip2num<$ipNum) {
			$Middle= intval(($EndNum + $BeginNum) / 2);
			//偏移指针到索引位置读取4个字节
			fseek($fd, $ipbegin + 7 * $Middle);
			$ipData1 = fread($fd, 4);
			if(strlen($ipData1) < 4) {
				fclose($fd);
				return 'System Error';
			}
			//提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
			$ip1num = implode('', unpack('L', $ipData1));
			if($ip1num < 0) $ip1num += pow(2, 32);
			//提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
			if($ip1num > $ipNum) {
				$EndNum = $Middle;
				continue;
			}
			//取完上一个索引后取下一个索引
			$DataSeek = fread($fd, 3);
			if(strlen($DataSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
			fseek($fd, $DataSeek);
			$ipData2 = fread($fd, 4);
			if(strlen($ipData2) < 4) {
				fclose($fd);
				return 'System Error';
			}
			$ip2num = implode('', unpack('L', $ipData2));
			if($ip2num < 0) $ip2num += pow(2, 32);
			//没找到提示未知
			if($ip2num < $ipNum) {
				if($Middle == $BeginNum) {
					fclose($fd);
					return 'Unknown';
				}
				$BeginNum = $Middle;
			}
		}
	   $ipFlag = fread($fd, 1);
		if($ipFlag == chr(1)) {
			$ipSeek = fread($fd, 3);
			if(strlen($ipSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
			fseek($fd, $ipSeek);
			$ipFlag = fread($fd, 1);
		}
	 
		if($ipFlag == chr(2)) {
			$AddrSeek = fread($fd, 3);
			if(strlen($AddrSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
	 
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr2 .= $char;
	 
			$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
			fseek($fd, $AddrSeek);
	 
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;
		} else {
			
			fseek($fd, -1, SEEK_CUR);
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;
	 
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
			while(($char = fread($fd, 1)) != chr(0)){
				$ipAddr2 .= $char;
			}
		}
		fclose($fd);
	 
		//最后做相应的替换操作后返回结果
		if(preg_match('/http/i', $ipAddr2)) {
			$ipAddr2 = '';
		}
		$ipaddr = "$ipAddr1 $ipAddr2";
		
		$ipaddr = comConvert::autoCSet($ipaddr,"gb2312",glbConfig::get('cbase','sys.cset'));
		return $ipaddr; //mb_convert_encoding($ipaddr,"utf-8","gb2312");
	}
	
	// 过滤处理
	function fill($addr){
		//美国 CZ88.NET 
		$addr = preg_replace('/CZ88.Net/is', '', $addr);
		$addr = preg_replace('/^s*/is', '', $addr);
		$addr = preg_replace('/s*$/is', '', $addr);
		if(preg_match('/http/i', $addr) || $addr == '') {
			$addr = 'Unknown';
		}
		return $addr;
	}
}

