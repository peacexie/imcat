<?php
//---------------------------------------------------------
//支付请求
//---------------------------------------------------------

require_once (dirname(__FILE__)."/common/CommonRequest.class.php");
class PayRequest extends CommonRequest {
	
	
	/**
	 * 生成支付跳转链接
	 */
	function getURL(){
		$paraString = $this->genParaStr();
		$domain = $this->getDomain();
		return $domain . $this->PAY_OPPOSITE_ADDRESS . "?" . $paraString;
	}
	
	function send(){
		return null;
	}
	
}


?>