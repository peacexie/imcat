<?php
/**
 * Class wmpBasic 基本接口，随微信规则更新
 *
 * 获取access token
 * 获取微信服务器IP地址
 */

class wmpBasic{

	public $cfg = array();
	public $actoken = '';
	
	// @var int access_token的有效期,目前为2个小时
	private $act_life = '90m'; //秒(1.5h) (200/2000次/天)
	
	// 获取access_token接口调用请求(网址)
	private $act_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
	
	// 获取微信服务器IP地址接口调用请求(网址)
	private $ip_url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=%s';

	function __construct($cfg=array()){
		$this->cfg = $cfg;
		$this->actoken = $this->getAccessToken();
		comHttp::setWay(1);
	}
    
	// 从缓存取；没有的化,先更新获取,再存缓存
    function getAccessToken($upd=0){
		global $_cbase; 
		if(!empty($_cbase['weixin']['actiks'][$this->cfg['appid']])){
			return $_cbase['weixin']['actiks'][$this->cfg['appid']];
		}
		$cfile = wysBasic::getCfpath($this->cfg['appid'], 'actik'); 
		$upath = tagCache::chkUpd($cfile,$this->act_life,0);
		if($upath){ 
			$data = @comFiles::get($cfile);
			$save = 0;
		}else{
			$url = sprintf($this->act_url,$this->cfg['appid'],$this->cfg['appsecret']); 
			$data = comHttp::doGet($url,3); 
			$databak = $data; //print_r($data);
			$save = 1;
		} 
		$data = wysBasic::jsonDecode($data,$this->act_url); //print_r($data);
		/*if(!empty($data['errcode'])){
			return wysBasic::debugError($data['errcode'],$data,$url,1);
		}*/
		if($save && !empty($data['access_token'])){ 
			$data && comFiles::put($cfile,$databak);
		}
		if(empty($data['access_token'])){ //一个进程中只取一次,保存供后续使用 //  && !empty($data['access_token'])
			$_cbase['weixin']['actiks'][$this->cfg['appid']] = @$data['access_token'];
		}
		return empty($data['access_token']) ? '' : $data['access_token'];
		
	}
	
	static function checkSignature($wecfg=array()){
		if(empty($wecfg['token'])) return false;
		$signature = @$_GET["signature"];
        $timestamp = @$_GET["timestamp"];
        $nonce = @$_GET["nonce"]; 
		$tmpArr = array($wecfg['token'], $timestamp, $nonce);
		sort($tmpArr, SORT_STRING); 
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
    //验证消息真实性
    static function checkValid($wecfg=array()){        
        if(!self::checkSignature($wecfg)) return false;
		#if(!strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')) return false;
		//测试号:HTTP_USER_AGENT=Mozilla/4.0
		//ip判断 ??? 又要远程抓一次数据，不要了。
		return true;
    }
	
    //获取微信服务器ip列表
    function getWeixinIP(){    
        $data = comHttp::doGet(sprintf($this->ip_url,$this->actoken),3); 
		return wysBasic::jsonDecode($data,$this->ip_url); 
    }



}
