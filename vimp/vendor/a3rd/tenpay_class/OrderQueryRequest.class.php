<?php
//---------------------------------------------------------
//订单查询请求
//---------------------------------------------------------


require_once (dirname(__FILE__)."/common/RetXmlRequest.class.php");
require_once (dirname(__FILE__)."/OrderQueryResponse.class.php");
class OrderQueryRequest extends RetXmlRequest{
	
	function send(){
		$respone = new OrderQueryResponse($this->retXmlHttpCall($this->NORMALQUERY_OPPOSITE_ADDRESS),$this->getSecretKey());
		return $respone;
	}
	
}


?>