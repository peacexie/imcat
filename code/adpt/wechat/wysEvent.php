<?php
(!defined('RUN_MODE')) && die('No Init');
// 事件响应操作
// 如果本系统修改,就改这个文件，不用改wmp*文件
// 各扩展系统需求变化很大,re开头的方法都加Base,扩展类里面不加Base,先检测执行无Base的方法,在找含有Base的方法

class wysEvent extends wmpMsgresp{

	public $_db = NULL;
	public $qrexpired = '5'; //5分钟过期...
	public $qrInfo = array();
	
	// 常用值
	public $eventKey = ''; //二维码场景ID
	public $fromName = ''; //微信open_id(用户ID)
	public $ghUser = ''; //开发者微信号
	public $subScanmsg = ''; //扫描关注消息
	
	public $sflag = ''; //扫描随机标记,用于安全检查

	function __construct($post,$wecfg){ 
		parent::__construct($post,$wecfg); 
		$method = $this->getMethod('Event'); 
		$this->_db = glbDBObj::dbObj();
		$this->init(); //echo $method;
		//setQrtable
		return $this->$method();
	}
	
	function init(){
		$this->eventKey = $this->post->EventKey; 
		$this->fromName = $this->post->FromUserName; 
		$this->ghUser = $this->post->ToUserName; 
	}
	
    // 取消关注事件
    function reUnsubscribeBase($re=0){ 
		//取消会员绑定,(是否删除会员？！)
		//$db->table($tabid)->data(array('enable'=>'1'))->where("kid='$id'")->update();
		#if($re) return;
		die('');
	}
    
    // 响应关注/扫描带参数二维码事件
    function reSubscribeBase(){ 
		global $_cbase;
		//查找关键字...
		$weres = new wysReply($this->post,$this->cfg,1); 
		$reauto = $weres->getKeyList('','follow_autoreply_info'); 
		$this->subScanmsg = empty($reauto) ? "您好，欢迎您关注 ".$_cbase['sys_name']."。\n" : "$reauto\n";
		if(!empty($this->eventKey)){ //未关注用户，扫描带参数二维码事件
			$this->eventKey = str_replace('qrscene_','',$this->eventKey);
			return $this->reScan();
		}
		die($this->remText($this->subScanmsg)); //回复
    }
    
	// 用户已关注时,扫描带参数二维码的事件推送
    function reScanBase(){ 
		if(!in_array(strlen($this->eventKey),array(5,10))){ die(''); }
		$this->qrInfo = $this->getQrinfo($this->eventKey); 
		$modKey = ucfirst(strtolower($this->qrInfo['smod'])); //echo "($modKey.$this->eventKey)";
		if(empty($modKey)){
			die($this->remText("二维码已过期，请重新获取二维码再操作！[$modKey:$this->eventKey]"));
		}
		$method = $this->getEvnact("scan$modKey",'scan'); //echo "<br>($method)";
		return $this->$method();
    }
	// ex
    function reScan($fromSub=''){
		return $this->reScanBase($fromSub);
	}

	// scan_Extra:无对应操作的处理
	function scan_Extra(){
		$msg = "无对应操作：[{$this->post->EventKey}], 请联系管理员！";
		$msg = $this->remText($msg);
		die($msg);
	}

	
    // 响应上报地理位置事件, 这里保存供使用
	// 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，或在进入会话后每5秒上报一次地理位置，
	// 公众号可以在公众平台网站中修改以上设置。
    function reLocationBase(){ 
		$this->savePos('auto');
		die('');
    }
    
    // 响应点击事件（即根据用户点击的按钮响应相应的回复）
    function reClickBase(){ 
		$eventKey = ucfirst(strtolower($this->eventKey)); 
		$eventKey = str_replace(array('_','-'),'',$eventKey);
		$method = $this->getEvnact("click$eventKey",'click');
		return $this->$method();
    }
	
	// 点击自定义菜单:无对应操作的处理
	function click_Extra(){
		$msg = "无对应操作：[{$this->post->EventKey}], 请联系管理员！";
		$msg = $this->remText($msg);
		die($msg);
	}
	
	// 用户点击自定义跳转URL菜单 事件处理
	function reViewBase(){
		die('');
	}
	
    // 摇一摇事件:ShakearoundUserShake
    function reShakearoundusershakeBase(){ 
		die('');
	}
	
	//获取场景二维码数据
    function getQrinfo($sid){
		global $_cbase;
		$timeNmin = $_cbase['run']['stamp']-($this->qrexpired*60*2); //10分钟 //saveState
		$row = $this->_db->table('wex_qrcode')->where("sid='$sid' AND atime>'$timeNmin'")->find();
		$this->sflag = basKeyid::kidRand(24,8);
		$this->_db->table('wex_qrcode')->data(array('sflag'=>$this->sflag,'openid'=>$this->fromName,))->where("sid='$sid'")->update();
		return $row;
	}
	
	function getEvnact($mcomm,$fix){ //echo "$mcomm,$fix"; 
		return method_exists($this,$mcomm) ? $mcomm : (method_exists($this,"{$mcomm}Base") ? "{$mcomm}Base" : "{$fix}_Extra"); 
	}

}
