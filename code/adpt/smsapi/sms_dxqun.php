<?php
// sms_dxqun；
class sms_dxqun{
		
	public $userid; // 序列号	
	public $userpw; // 密码
	public $baseurl = 'http://http.chinasms.com.cn'; // http://http.dxsms.com, http://http.chinasms.com.cn	
	public $post; // post对象

	// 初始化
	function __construct($cfgs=array()){
		$this->arr['uid'] = $this->userid = $cfgs['user'];
		$this->arr['pwd'] = $this->userpw = $cfgs['pass'];;
	}
	
	/** sms_dxqun；
	 * 超过70个字符会自动分多条发送。短信内容不支持空格(webservice接口支持)。
	 * 建议每次提交在100个号内，超过请自行做循环
	 * 每次GET提交请不要大于100个号码。Post方式每次提交请不要大于5000个号码，多个手机号码用 , 英文逗号隔
	 * HTTP接口发送和接收的短信内容必须是GB2312编码。HTTP发送,内容不支持空格
	 */
	function sendSMS($mobiles,$content){
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		$content = comConvert::autoCSet($content,cfg('sys.cset'),"gb2312");
		$content = str_replace(array(' ','　',"\r","\n"),'',$content); // 短信内容不支持空格???
		$arr = $this->arr;
		$arr['phone'] = $mobiles;
		$arr['message'] = $content;
		$html = comHttp::doPost("{$this->baseurl}/tx/", $arr, 3); 
		echo basStr::filText($html);
		// 100{&}13912341234||短信测试回复||2008-05-27 12:10:11||1068112227282{&}15912343333||短信测试回复2||2008-05-27 13:11:11||106811222728200
		if(empty($html)){
			return array('-1','(sms-Server Error)');
		}elseif(substr($html,0,3)>'100'){
			$emsg = $this->reInfo(substr($html,0,3));
			return array('-1',"$emsg"); 	
		}else{
			return array('1','OK'); 	
		}
	}
	
	// 余额查询 
	function getBalance(){
		$path = "/mm/?uid={$this->userid}&pwd=".md5($this->userpw)."";
		$html = comHttp::doPost("{$this->baseurl}/mm/", $this->arr, 3); 
		// 100||22348
		if(empty($html)){
			return array('-1','(sms-Server Error)');
		}elseif(substr($html,0,5)=='100||'){
			return array('1', substr($html,5));
		}else{
			return array('-1',$this->reInfo(substr($html,0,3)));
		}
	}
	
	// 返回值-描述 对应表
	function reInfo($no){
		$a = array(
			'100' => '发送成功',
			'101' => '验证失败！',
			'102' => '短信不足！',
			'103' => '操作失败！',
			'104' => '非法字符！',
			'105' => '内容过多！',
			'106' => '号码过多！',
			'107' => '频率过快！',
			'108' => '号码内容空',
			'109' => '账号冻结！', 
			'110' => '禁止频繁单条发送！',
			'111' => '系统暂定发送！',
			'112' => '号码错误！',
			'113' => '定时时间格式不对！',
			'114' => '账号被锁，10分钟后登录！',
			'115' => '连接失败！',
			'116' => '禁止接口发送！',
			'117' => '绑定IP不正确！',
			'120' => '系统升级！',
		);	
		return isset($a[$no]) ? $no.':'.$a[$no] : "$no:(未知错误)";
	}

}
