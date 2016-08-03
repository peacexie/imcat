<?php
//---------------------------------------------------------
//返回xml数据的请求超类
//---------------------------------------------------------

require_once (dirname(__FILE__)."/CommonRequest.class.php");
require_once (dirname(__FILE__)."/util/HttpClientUtil.php");
require_once (dirname(__FILE__)."/util/XmlParseUtil.php");
class RetXmlRequest extends CommonRequest {
	
	//获取url
	function  getURL($opposite_address){
		$paraString = $this->genParaStr();
		$domain = $this->getDomain();
		return $domain . $opposite_address . "?" . $paraString;
	}
	
	function retXmlHttpCall($opposite_address){
		
		$queryXml = null;
		$objH = new HttpClientUtil();
		
		try {
		    $queryXml = $objH->httpClientCall($this->getURL($opposite_address),$this->getInputCharset());
		} catch (Exception $e) {
			throw new SDKRuntimeException("http请求失败:" + $e.getMessage());
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
		$xmlParse = new XmlParseUtil();
		return $xmlParse->openapiXmlToMap($queryXml,$this->getInputCharset());
	}
}


?>