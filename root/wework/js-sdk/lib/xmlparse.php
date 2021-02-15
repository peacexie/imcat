<?php
include_once "errorCode.php";

/**
 * XMLParse class
 *
 * 提供提取消息格式中的密文及生成回复消息格式的接口.
 */
class XMLParse
{

	/**
	 * 提取出xml数据包中的加密消息
	 * @param string $xmltext 待提取的xml字符串
	 * @return string 提取出的加密消息字符串
	 */
	public function extract($xmltext)
	{
		try {
			$xml = new DOMDocument();
			$xml->loadXML($xmltext);
			$array_e = $xml->getElementsByTagName('Encrypt');
			$array_a = $xml->getElementsByTagName('ToUserName');
			$encrypt = $array_e->item(0)->nodeValue;
			$tousername = $array_a->item(0)->nodeValue;
			return array(0, $encrypt, $tousername);
		} catch (Exception $e) {
			print $e . "\n";
			return array(ErrorCode::$ParseXmlError, null, null);
		}
	}

	/**
	 * 生成xml消息
	 * @param string $encrypt 加密后的消息密文
	 * @param string $signature 安全签名
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
	 */
	public function generate($encrypt, $signature, $timestamp, $nonce)
	{
		$format = "<xml>
					   <Encrypt><![CDATA[%s]]></Encrypt>
					   <MsgSignature><![CDATA[%s]]></MsgSignature>
					   <TimeStamp>%s</TimeStamp>
					   <Nonce><![CDATA[%s]]></Nonce>
				   </xml>";
		return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
	}

	/**
	 * 提取出xml数据包中的加密消息
	 * @param string $xmltext 待提取的xml字符串
	 * @return string 提取加密后回调模式接口验证需要的参数
	 */
	public function extractCallbackParamter($xmltext)
	{
		try {				
			$xml = new DOMDocument();
			$xml->loadXML($xmltext);
			$Encrypt = $xml->getElementsByTagName('Encrypt')->item(0)->nodeValue;
			$MsgSignature = $xml->getElementsByTagName('MsgSignature')->item(0)->nodeValue;
			$TimeStamp = $xml->getElementsByTagName('TimeStamp')->item(0)->nodeValue;
			$Nonce = $xml->getElementsByTagName('Nonce')->item(0)->nodeValue;
			
			return array($Encrypt, $MsgSignature, $TimeStamp,$Nonce);
		} catch (Exception $e) {
			print $e . "\n";
			return array(ErrorCode::$ParseXmlError, null, null);
		}
	}

	/**
	 * 生成xml消息
	 * @param string $encrypt 加密后的消息密文
	 * @param string $agentId 应用ID
	 * @param string $tousername 企业ID	 
	 */
	public function generateCallbackXml($encrypt, $agentId, $tousername)
	{
		$format = "<xml>
					   <ToUserName><![CDATA[%s]]></ToUserName>
					   <AgentID><![CDATA[%s]]></AgentID>
					   <Encrypt>%s</Encrypt>					   
				   </xml>";
		return sprintf($format,$tousername,$agentId,$encrypt);
	}

}

?>