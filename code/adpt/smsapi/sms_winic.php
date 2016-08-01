<?php
// sms_winic；
class sms_winic{
	
	public $userid; // 序列号
	public $userpw; // 密码
	public $baseurl = 'http://service.winic.org';
	public $post; // post对象

	// 初始化
	function __construct($cfgs=array()){
		$this->arr['uid'] = $this->userid = $cfgs['user'];
		$this->arr['pwd'] = $this->userpw = $cfgs['pass'];
		$this->arr['id'] = $this->userid;
	}
	
	/** sms_winic；
	 * 超过70个字符会自动分多条发送。短信内容不支持空格(webservice接口支持)。
	 * 建议每次提交在100个号内，超过请自行做循环
	 * 每次GET提交请不要大于100个号码。Post方式每次提交请不要大于5000个号码，多个手机号码用 , 英文逗号隔
	 * HTTP接口发送和接收的短信内容必须是GB2312编码。HTTP发送,内容不支持空格
	 */
	function sendSMS($mobiles,$content){
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		$content = comConvert::autoCSet($content,glbConfig::get('cbase','sys.cset'),"gb2312");
		$content = str_replace(array(' ','　',"\r","\n"),'',$content); // 短信内容不支持空格???
		$arr = $this->arr;
		$arr['to'] = $mobiles;
		$arr['content'] = $content;
		$html = comHttp::doPost("{$this->baseurl}/sys_port/gateway/", $arr, 3); 
		//echo basStr::filText($html);
		// -02/Send:2/Consumption:0/Tmoney:0/sid:
		if(empty($html)){
			return array('-1','(sms-Server Error)');
		}elseif(substr($html,0,3)=='nul' || substr($html,0,1)=='-'){
			$emsg = $this->reInfo(substr($html,0,3));
			return array('-1',"$emsg"); 	
		}else{
			return array('1','OK'); 	
		}
	}
	
	// 余额查询
	function getBalance(){ 
		$html = comHttp::doPost("{$this->baseurl}:8009/webservice/public/remoney.asp", $this->arr, 3); 
		unset($this->arr['uid']);
		//echo basStr::filText($html);
		// .5
		if(empty($html)){
			return array('-1','(sms-Server Error)');
		}elseif(substr($html,0,3)=='nul' || substr($html,0,1)=='-'){
			$emsg = $this->reInfo(substr($html,0,3));
			return array('-1',"$emsg"); 	
		}else{
			if(substr($html,0,1)=='.') $html = "0$html";
			return array('1',$html); 	
		}
	}
	
	// 返回值-描述 对应表
	function reInfo($no){
		$a = array(
			'nul' => '无接收数据',
			'000' => '操作成功',
			'-01' => '当前账号余额不足！',
			'-02' => '当前用户ID错误！',
			'-03' => '当前密码错误！',
			'-04' => '参数不够或参数内容的类型错误！',
			'-05' => '手机号码格式不对！',
			'-06' => '短信内容编码不对！',
			'-07' => '短信内容含有敏感字符！',
			'-09' => '系统维护中.. ',
			'-10' => '手机号码数量超长！', //短信内容超长！（70个字符）目前已取消
			'-11' => '短信内容超长！',
			'-12' => '其它错误！',
		);	
		return isset($a[$no]) ? $no.':'.$a[$no] : "$no:(未知错误)";
	}

}
