<?php
(!defined('RUN_MODE')) && die('No Init');

class wmpJssdk extends wmpBasic{

	private $cacheFull = '';
	// @var int access_token的有效期,目前为2个小时
	private $act_life = 5400; //秒(1.5h) (200/2000次/天)

	function __construct($cfg=array()){
		parent::__construct($cfg); 
		$this->cacheInit();
	}

	function getSignPackage() {
		$jsapiTicket = $this->getJsApiTicket();
		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$timestamp = time();
		$nonceStr = $this->createNonceStr();
		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		$signPackage = array(
			"appId"     => $this->cfg['appid'],
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage; 
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket() {
	// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
	$data = json_decode(comFiles::get($this->cacheFull));
	if ($data->expire_time < time()) {
		$accessToken = $this->getAccessToken();
		// 如果是企业号用以下 URL 获取 ticket
		// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
		$res = comHttp::doGet($url,3);
		$res = json_decode($res);
		$ticket = $res->ticket; 
		if ($ticket) {
			$data->expire_time = time() + $this->act_life;
			$data->jsapi_ticket = $ticket;
			$this->cacheSave($data);
		}
	}else{
		$ticket = $data->jsapi_ticket;
	} //echo $ticket;
	return $ticket;
	}

	private function cacheInit() {
		$this->cacheFull = DIR_DTMP.str_replace('(appid)',$this->cfg['appid'],wysBasic::$cache_path['jstik']); 
		if(!file_exists($this->cacheFull)){ 
			$this->cacheSave(array('jsapi_ticket'=>'','expire_time'=>''));
		}
		//die($this->cacheFull);
	}
  
	private function cacheSave($data) {
		$fp = fopen($this->cacheFull, "w");
		fwrite($fp, json_encode($data));
		fclose($fp);
	}

  /*private function getAccessToken(){}*/
  /*private function httpGet($url){}*/
  
}

