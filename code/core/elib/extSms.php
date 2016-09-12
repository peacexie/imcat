<?php

// 手机短信接口类
 
class extSms{

	public  $cfg_mchar = 70; // 一条信息,文字个数(小灵通65个字)
	public  $cfg_mtels = 200; // 一次发送,最多200个手机号码个数
	
	public  $api	   = ''; //api接口类型(提供商)
	public  $smsdo	 = NULL; //api对象
	public  $cfgs	  = array(); //api配置
	
	public  $cnow	  = array('name'=>'-','unit'=>'-'); //now配置
	public  $amap	  = array(); //api列表
	//public  $name	  = ''; //name
	
	//function __destory(){  }
	function __construct(){ 
		require(DIR_CODE."/adpt/smsapi/api_cfgs.php"); // 加载
		$_cfgs = glbConfig::read('sms','ex');
		$api = @$_cfgs['cfg_api'];
		if($api && isset($_apis[$api])){ 
			$this->api = $api;
			$this->cfgs = $_cfgs; 
			$this->cnow = $_apis[$api];
			//$this->name = $this->cnow['name'];
			$class = "sms_$this->api";
			// 统一实例化一个 api对象 // load sms libs
			require(DIR_CODE."/adpt/smsapi/sms_{$this->api}.php"); // 加载
			$this->smsdo = new $class($_cfgs); 
		}
		$this->amap = $_apis;
	}
	
	// 短信接口是否关闭
	function isClosed(){
		if(empty($this->api)){
			return true;
		}else{
			return false;
		} //&&$sms_cfg_api!='(close)'
	}
	
	// 余额查询
	// 结果说明：array(1,1234.5): 成功,余额为1234.5；array(-1,'失败原因'): 
	function getBalance(){ 
		return $this->smsdo->getBalance();	
	}
	
	/** 短信发送，支持短信模版替换，
	 * @param	string	$mobiles 	手机号码,参考sendSMS()
	 * @param	string	$tpl 		支持模版，如：{$subject}{$name}标记
	 * @param	array	$source		替换源：array('subject'=>'hellow corp!','name'=>'peace',)
	 * @param	string	$type 		发送方式/发送身份,参考sendSMS()
	 * @return	array	---		结果数组,参考sendSMS()
	 **/
	function sendTpl($mobiles,$tpl,$source,$limit=1){
		$tpl = str_replace(array("\r\n","\r","\n"),array(' ',' ',' '),$tpl);
		if(preg_match_all('/{\s*(\$[a-zA-Z_]\w*)\s*}/i', $tpl, $matchs)){
			if(!empty($matchs[0])){
				foreach($matchs[0] as $v){
					$k = str_replace(array('{','$','}'),'',$v);
					$val = isset($source[$k]) ? $source[$k] : (isset($GLOBALS[$k]) ? $GLOBALS[$k] : "{\$$k}");
					$tpl = str_replace($v,$val,$tpl);
				}
			}
		}
		return $this->sendSMS($mobiles,$tpl,$limit);
	}
	
	/** 短信发送
	 * @param	string	$mobiles 	手机号码,array/string(英文逗号分开)
	 * @param	string	$content 	255个字符以内
	 * @param	string	$type 		发送方式,发送身份 ：
	 *					scom=默认,普通会员发送,检测余额, 
	 *					sadm=管理员(不检测余额), 
	 *					ctel=手机认证(不检测登陆,每次一个号码,70字以内)
	 *					$uid=会员id(整数),以$uid的用户发送并扣余额,(!!!)调用发送的地方请控制好权限,否则,会扣完$uid的余额
	 * @return	array	---		结果数组,如：array(1,'操作成功'): 
	 **/
	function sendSMS($mobiles,$content,$limit=1){
		global $_cbase; 
		$db = glbDBObj::dbObj();
		// 格式化 $mobiles,$content, 
		$atel = $this->telFormat($mobiles);
		$amsg = $this->msgCount($content);
		if(empty($atel)) return array('-2',lang('sms_errtel'));	
		if(empty($amsg[0])) return array('-2',lang('sms_errmsg'));
		$nmsg = count($atel)*$amsg[1];
		// 需扣费计算条数,检查余额
		$balance = $this->smsdo->getBalance(); 
		if((float)$balance[1]<=0){
			$mobiles = implode(',',$atel);
			$this->balanceWarn("--tels:$mobiles\n --cmsg:$content"); //写记录
			return array('-2',lang('sms_charge0'));		
		} //print_r("$limit,$limit<$nmsg");
		if($limit && $limit<$nmsg){
			return array('-2',lang('sms_charged'));	
		}
		// 发送及结果
		if(count($atel)>$this->cfg_mtels){ // 分组发送
			$groups = array_chunk($atel,$this->cfg_mtels);
			$res = array('-2',lang('sms_msenderr'));
			$flag = false; //成功标记
			foreach($groups as $group){ 
				$res_temp = $this->smsdo->sendSMS($group,$content);
				if($res_temp[0]=='1'){ //只要一组发送成功,则都算成功.
					$res = $res_temp;	
				}
			}
		}else{
			$res = $this->smsdo->sendSMS($atel,$content);
		}	
		// 写记录-db
		$stel = implode(',',$atel); 
		if(strlen($stel)>255) $stel = substr($stel,0,240).'...'.substr($stel,strlen($stel)-5,255);
		$data = array( //$kid = glbDBExt::dbAutID('xtest_keyid','yyyy-md-','5.6'); 
			'kid'=>basKeyid::kidTemp(),
			'tel'=>$stel,'msg'=>basReq::in($amsg[0]),
			'res'=>implode(':',$res),'api'=>$this->api,'amount'=>$nmsg,
			'aip'=>$_cbase['run']['userip'],'atime'=>$_cbase['run']['timer'],'auser'=>'peace',
		);
		$db->table('plus_smsend')->data($data)->insert();
		// 扣钱 for 0test_balance.txt
		if($this->api=='0test' && $res[0]=='1'){
			$this->smsdo->deductingCharge($nmsg);
		}
		return $res;

	}
	
	/** 余额报警检测,余额报警记录
	 * @param	int		$flag 	int/string数字/
	 *					数字,多少小时被修改(记录了余额不足)过,
	 *					flag=str,记录信息内容
	 * @return	NULL	
	 **/
	function balanceWarn($flag){
		$file = "debug/balance_apiwarn.wlog"; 
		comFiles::chkDirs($file,'tmp');
		if(is_numeric($flag)){ //检查文件,多少时间(day)内修改过
			return tagCache::chkUpd("/$file",'24h');
		}else{ 
			$onlineip = glbConfig::get('cbase', 'run.userip'); $data = ''; 
			if(file_exists($file)){
				$data = comFiles::get($file);
			}
			$fp = fopen(DIR_DTMP."/$file", 'a');
			$data = date('Y-m-d H:i:s')."^ ".'mname'." ^ $onlineip \n $flag\r\n\r\n$data";
			flock($fp, 2); fwrite($fp, $data); fclose($fp);
		}
	}

	// 电话号码 格式化/过滤
	// 初始的电话号码array/string
	// 格式化并过滤后的电话号码
	function telFormat($tel){
		if(is_string($tel)){
			$tel = str_replace(array("-","("," ",')'),'',$tel);
			$tel = str_replace(array("\r\n","\r","\n",';'),',',$tel);
			$arr = explode(',',$tel);
		}else{
			$arr = $tel;	
		}
		$arr = array_filter($arr);
		$re = array();
		for($i=0;$i<count($arr);$i++){
			//  手机/^1\d{4,10}$/; 95168合法号码/^[1-9]{1}\d{4,10}$/; 0769-12345678小灵通
			if(preg_match('/^\d{5,12}$/',$arr[$i])) $re[] = $arr[$i];
		}
		return $re;	
	}
	// 短信内容 截取/计数
	// param	string	$msg 	初始的短信内容
	// param	int		$slen 	最多截取多少文字
	// return	array	$re		返回array(文字,信息条数,文字个数)
	function msgCount($msg,$slen=255){
		$cset = glbConfig::get('cbase', 'sys.cset');
		$cnt = mb_strlen($msg, $cset);
		if($cnt>255){
			$msg = mb_substr($str, 0, 250, $cset); 
			$cnt = 250;
		}
		if($cnt>$this->cfg_mchar){ // >70字
			$ncnt = ceil($cnt/($this->cfg_mchar-3)); //(70-3)个字算一条信息
		}else{
			$ncnt = 1;
		}
		return array($msg,$ncnt,$cnt); 
	}
	
	// 部分接口有此方法
	function chargeUp($charge){
		return $this->smsdo->chargeUp($charge);
	}

}
