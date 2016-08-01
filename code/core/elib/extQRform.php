<?php

// extQRform:扫描二维码 认证表单 --- 警告:安全性有待讨论; 防止垃圾意义不大! 
// 既然图片认证码可识别,那二维码一样可识别,而且识别率更高更准确; 而设别等问题,可以伪造ua与header
// Peace看到如下地址(点登录)，异想天开的到的结果
// http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login

class extQRform{
	
	// cfgs
	private $db = NULL; 
	private $uid = ''; 
	private $mod = ''; 
	private $timeqr = 60;
	private $timeout = 600;
	
	// 配置接口
	function __construct() { 
		global $_cbase;
		$_cbase['run']['isDemo'] = 0;
		$this->db = glbDBObj::dbObj(); 
		$this->db = $this->db->table('pwec_qrcode');
		$uid = comSession::guid('','qr'); $this->uid = $uid['sid']; 
		$this->mod = basReq::val('mod','adm_login'); 
	}
	
	// 
	function qrVstr(){
		global $_cbase;
		$stamp = $_cbase['run']['stamp'];
		$uid = $this->uid; $mod = $this->mod;
		$oldata = $this->db->where("uid='$uid' AND mod='$mod'")->order("atime DESC")->find();
		if(!empty($oldata['atime']) && $stamp-$oldata['atime']<$this->timeqr){ 
			die('Time-Frequency');
		}else{
			$oldata = array();	
		}
		if(empty($oldata['sid'])){
			$sid = basKeyid::getYYYYMMDD().basKeyid::getTimer().basKeyid::kidRand('',12);
			$arr = array('mod'=>$mod,'uid'=>$uid,'sid'=>$sid,'stat'=>0,'atime'=>$stamp,);
			$ext = array('aip'=>$_cbase['run']['userip'],'auser'=>$_cbase['run']['userag'],);
			$this->db->data(basReq::in($arr+$ext))->insert();
		}else{
			$sid = $oldata['sid'];
			$stamp = $oldata['stamp'];
		}	
		$check = comConvert::sysEncode("$mod.$sid.$stamp",'',32); //$uid.
		$arr = array();
		foreach(array('uid','sid','check','stamp') as $k){
			$arr[$k] = $$k; 	
		}
		$str = comParse::jsonEncode($arr); 
		return $str;
	}
	
	// 
	function qrVauto(){ 
		global $_cbase;
		$uid = $this->uid; $mod = $this->mod;
		$sid = basReq::val('sid','');
		$oldata = $this->db->where("sid='$sid' AND mod='$mod' AND uid='$uid'")->order("atime DESC")->find();
		if(!empty($oldata['atime']) && $oldata['stat']>0){ 
			$info = ""; 
			foreach(array('sid','mod','uid') as $k) $info .= "<input name='$k' type='hidden' value='".$$k."'>";
			$str = "(isOKStart!{$info}isOKEnd!)";
		}else{
			$str = "[Error!](sid='$sid' AND mod='$mod' AND uid='$uid')";
		}
		return $str;
	}
	// 
	function qrVres(){ 
		global $_cbase;
		$uid = $this->uid; $mod = $this->mod;
		$sid = basReq::val('sid','');
		$check = basReq::val('check','');
		$stamp = basReq::val('stamp','');
		$cenc = comConvert::sysEncode("$mod.$sid.$stamp",'',32); 
		$ctime = $_cbase['run']['stamp'];
		$flag = 'OK';
		if($ctime-$stamp>$this->timeout){ //10分钟有效
			$flag = "Error Timeout {$this->timeout}(s)";
		}elseif(!basEnv::isMobile()){
			$flag = 'Error NOT Mobile';
		}elseif($check==$cenc){ 
			$oldata = $this->db->where("sid='$sid' AND mod='$mod'")->order("atime DESC")->find();
			if(empty($oldata)){
				$flag = "Error Check:<br>check=$check<br>cenc=$cenc";
			}elseif($stamp-$oldata['atime']>1800){ 
				$oldata = array();
				$flag = "Error Check:<br>stamp=$stamp<br>atime={$oldata['atime']}"; 
			}else{
				$data = array('stat'=>1,'etime'=>$stamp,'eip'=>$_cbase['run']['userip'],'euser'=>$_cbase['run']['userag']);
				$this->db->data(basReq::in($data))->where("sid='$sid' AND mod='$mod'")->update();
			}
		}else{
			$flag = 'Error Unknow!';
		}
		$arr = array();
		foreach(array('uid','sid','check','stamp','cenc','ctime') as $k){
			$arr[$k] = $$k; 	
		}
		return array($flag,$arr);
	}	
}
