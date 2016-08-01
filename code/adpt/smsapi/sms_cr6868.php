<?php
/**
*** 以下问题, 请与短信供应上联络：
-1- (和平鸽)你们那个接口，延时厉害啊！(前天我在你们网站测试时，是很快的。)
--- 创瑞短信公司的客户经理 您现在不是免审,早上这一块发短信的人又多。延时肯定的会有的。
#2# 当发送含有一些关键字时；
### 当时显示发送成功，其实没有发送成功；
### 进cr6868.com网站后台可发现有“驳回”提示；
### 但我们系统已经是按当时状态(成功)的来处理，这个是个问题。
 */
// sms_cr6868；
class sms_cr6868{
	
	public $userid; // 序列号
	public $userpw; // 密码
	public $baseurl = 'http://web.cr6868.com/asmx/smsservice.aspx';
	public $arr = array(); // 参数
	public $way = 1; // http方式

	// 初始化
	function __construct($cfgs=array()){
		$this->arr['name'] = $this->userid = $cfgs['user'];
		$this->arr['pwd'] = $this->userpw = $cfgs['pass'];
		comHttp::$way = $this->way;
	}
	
	/**
	 * sms_cr6868；
	 * 发送内容（1-500 个汉字）UTF-8编码
	 * http://web.cr6868.com/asmx/smsservice.aspx?name=13537432147&pwd=xxx&content=test测试msg[和平鸽]&mobile=13537432147&type=pt
	 */
	function sendSMS($mobiles,$content){
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		$content = str_replace(array(' ','　',"\r","\n","&","#"),'',$content); // 短信内容不支持空格???
        //$content = str_replace(array('[',']'),array('【','】'),$content); // 具体咨询短信供应商
		$content = comConvert::autoCSet($content,glbConfig::get('cbase','sys.cset'),"utf-8");
		$arr = $this->arr;
		$arr['type'] = 'pt';
		$arr['content'] = $content; //urlencode($content); 
		$arr['mobile'] = $mobiles; 
		$html = comHttp::doPost("$this->baseurl", $arr, 3); 
		$re = $this->fmtInfo($html); //var_dump($html); 
		//if($re[0]=='1') $re[1] = '发送成功';
		return $re;		
	}
	
	/**
	 * 余额查询 
	 * @return double 余额
	 */
	function getBalance(){
		//return array(1,1234);
		$arr = $this->arr;
		$html = comHttp::doGet("$this->baseurl?name=$this->userid&pwd=$this->userpw&type=balance", 3); //var_dump($html);  
		$re = $this->fmtInfo($html);
		//if(substr($html,0,1)=='.') $html = "0$html";
		if($re[0]=='1') $re[1] = substr($re[1],2);
		return $re;	
	}

	/**
	 * 返回值-描述 对应表
	 - 0,type is not true ,
	 - 0,6
	 - 10,用户名或密码错误
	 - 1:0,20150414130559306617 
	 re stat,res
	 */
	function fmtInfo($html)
	{
		@$arr = explode(',',$html);
		if(empty($html) || strpos($html,',')<=0){
			return array('',"sms-Server Error:[$html]");	
		}elseif($arr[0]==='0'){
			return array('1',$html);	
		}else{
			return array('-1',"Error:[$html]");	
		}
	}

}

/*
'0'  => '操作成功', 
'1'  => '含有敏感词汇！', 
'2'  => '余额不足',
'3'  => '没有号码',
'4'  => '包含sql语句',
'10' => '账号不存在',
'11' => '账号注销',
'12' => '账号停用',
'13' => 'IP鉴权失败',
'14' => '格式错误',
'-1' => '系统异常',
'-2' => '其它错误！',
*/
