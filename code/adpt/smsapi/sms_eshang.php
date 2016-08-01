<?php
// sms_eshang；
class sms_eshan{
		
	public $userid; // 用户名	
	public $userpw; // 密钥	
	public $baseurl = 'http://sms.eshang8.com/jdk/';	
	public $xml; // post对象

	function __construct($cfgs=array()){
		$this->userid = $cfgs['user'];
		$this->userpw = $cfgs['pass'];
		$this->xml = new DOMDocument();
		//$this->getInit();
	}
	
	/** sms_eshang8；
	 * 信息内容(GBK编码)，包含中文的短信长度小于等于70个字符，纯英文的短信长度小于等于150个字符
	 */
	function sendSMS($mobiles,$content){
		global $_cbase; 
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		if($mcharset=='utf-8') $content = iconv($mcharset,'gbk',$content); 
		//$content = str_replace(array(' ','　',"\r","\n"),'',$content); // 短信内容不支持空格???
		// >70字分割 ?????????????? 
		$content = urlencode($content); 
		$path = "?esname={$this->userid}&key={$this->userpw}&phone=$mobiles&msg=$content&smskind=1";
		$this->xml->load("$this->baseurl$path");
		return $this->reInfo();
	}
	
	// 余额查询 
	function getBalance(){
		$path = "?esname={$this->userid}&key={$this->userpw}&smskind=1";
		$this->xml->load("$this->baseurl$path");
		return $this->reInfo('PayCount');
	}
	
	// 返回值-描述 对应表
	function reInfo($flag=''){
		$root1 = $this->xml->getElementsByTagName("root")->item(0); 
		$cnt = $root1->getElementsByTagName( "PayCount" )->item(0)->nodeValue;
		$res = $root1->getElementsByTagName( "result" )->item(0)->nodeValue;
		$err = $root1->getElementsByTagName( "ErrorDesc" )->item(0)->nodeValue;
		if($mcharset!='utf-8') $err = iconv('utf-8','gbk',$err); 
		$no = $res; 
		if($no=='1') $no = '-1';
		if($no=='0') $no = '1'; //成功统一返回1
		$a = array( //0成功 1发送失败2参数错误3屏蔽字4验证失败
			'-1' => '发送失败',
			'1'   => '操作成功',
			'2' => '参数错误！',
			'3' => '屏蔽字！',
			'4' => '验证失败！',
		);
		$msg = isset($a[$no]) ? $a[$no] : '(未知错误)';
		if($flag=='PayCount'){
			if(substr($cnt,0,1)=='.') $html = "0$cnt";
			if($res=='1') return array('1',$cnt); 
			else return array('-1',0); 
		}
		return array($no,$msg);
	}

}
