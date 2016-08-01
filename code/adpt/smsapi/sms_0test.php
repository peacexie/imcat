<?php

/**
 * 仅测试使用，用于测试系统其它流程；
 * 具体操作不会发短信，仅写一个文件记录表示发短信
 */
class sms_0test{
	
	public $userid; // 序列号
	public $userpw; // 密码
	public $bfile; // 余额文件

	// 初始化
	function __construct($cfgs=array()){
		$this->userid = $cfgs['user'];
		$this->userpw = $cfgs['pass'];
		// 余额 
		$file = "store/0test_balance.txt"; 
		comFiles::chkDirs($file,'tmp');
		$file = DIR_DTMP."/$file"; 
		if(!file_exists($file)){
			$fp = fopen($file, 'wb');
			$fee = rand(50,100);
			flock($fp, 2); fwrite($fp, $fee); fclose($fp);
		}
		$this->bfile = $file;
	}
	
	// 具体操作不会发短信
	function sendSMS($mobiles,$content){
		$rnd = rand(1,1000); 
		if($rnd<900){ // 模拟,90%情况下成功
			// 扣钱 test_balance.txt
			return array(1,"OK");
		}else{
			return array(-1,'失败!');
		}
	}
	
	// 余额查询 
	function getBalance(){
		$rnd = rand(1,1000);
		if($rnd<990){ // 模拟,99.0%情况下成功
			$cnt = comFiles::get($this->bfile);
			return array('1',$cnt);
		}else{
			return array('-1','失败!');
		}	
	}
	
	// 充值
	function chargeUp($count){
		$rnd = rand(1,1000);
		if($rnd<900){ // 模拟,90%情况下成功
			$cnt = comFiles::get($this->bfile);
			$cnt += $count;
			$fp = fopen($this->bfile, 'wb');
			flock($fp, 2); fwrite($fp, $cnt); fclose($fp);
			return array('1',$cnt);
		}else{
			return array('-1','失败!');
		}	
	}
	
	// 扣费
	function deductingCharge($count){
		$cnt = comFiles::get($this->bfile);
		$cnt -= $count; 
		if((float)$cnt<0) $cnt = 0; 
		$fp = fopen($this->bfile, 'wb');
		flock($fp, 2); fwrite($fp, $cnt); fclose($fp);
		return array(1,$cnt);
	}

}

// 附加说明
// none
