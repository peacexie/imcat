<?php
//---------------------------------------------------------
//响应基础类，定义相关参数及处理
//---------------------------------------------------------

include_once (dirname(__FILE__)."/SDKRuntimeException.class.php");
include_once(dirname(__FILE__)."/util/CommonUtil.php");
include_once(dirname(__FILE__)."/util/MD5SignUtil.php");
class CommonResponse {
	var $RETCODE = "retcode";
	var $RETMSG = "retmsg";
	var $TRADE_STATE = "trade_state";
	var $TRADE_STATE_SUCCESS = "0";
	/** 密钥 */
	var $secretKey;
	var $parameters = array();
	
	function __construct($paraMap,$secretKey) {
		try {
			unset($this->parameters);
			$this->secretKey = $secretKey;
			$this->parameters = $paraMap;
			if(!$this->isRetCodeOK()){
				throw new SDKRuntimeException("服务调用异常:" . $this->getPayInfo(). "<br>");
			}
			
			$this->verifySign();
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
		
	}
	
	function CommonResponse() {
	}
	
	protected function verifySign(){
		try {
		if (null == $this->parameters) {
			throw new SDKRuntimeException("parameters为空!". "<br>");
		}
		
		$sign = $this->getParameter("sign");
		if (null == $sign) {
			throw new SDKRuntimeException("sign为空!". "<br>");
		}
		$charSet = $this->getParameter("input_charset");
		if (null == $charSet) {
			$charSet = Constants::DEFAULT_CHARSET;
		}
		$signStr = CommonUtil::formatQueryParaMap($this->parameters, false);
		if (null == $this->secretKey) {
			throw new SDKRuntimeException("签名key为空!". "<br>");
		}
		if(!MD5SignUtil::verifySignature($signStr,$sign,$this->secretKey)){
			throw new SDKRuntimeException("返回值签名验证失败!". "<br>");
		}
		return true;
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	/**
	 * 获取密钥
	 */
	function getSecretKey(){
		return $this->key;
	}
	/**
	 * 设置密钥
	 * 
	 * @param secretKey
	 *            密钥
	 */
	function setSecretKey($secretKey){
		$this->key = $secretKey;
	}
	/**
	*获取参数值
	*/
	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}
	
	/**
	*设置参数值
	*/
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	
	/**
	 * 接口调用是否成功
	 */
	function isRetCodeOK(){
		return "0"==$this->getRetCode();
	}
	
	function isPayed(){
		return $this->isRetCodeOK() && $this->TRADE_STATE_SUCCESS == $this->getParameter($this->TRADE_STATE);
	}
	/**
	 * 获取接口返回码
	 */
	function getRetCode(){
		return $this->getParameter($this->RETCODE);
	}
	/**
	 * 获取错误信息
	 */
	function getPayInfo(){
	    $info = $this->getParameter($this->RETMSG);
		if(null == CommonUtil::trimString($info) && !$this->isPayed()){
		   $info = "订单尚未支付成功";
		}
		return $info;
	}
	
	
}


?>