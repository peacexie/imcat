<?php
//---------------------------------------------------------
//处理返回的xml数据
//---------------------------------------------------------

class XmlParseUtil{
	
	function openapiXmlToMap($xml, $charset) {
		$hashMap = array();
		$stringDOM = new DOMDocument();
		try{
			@$stringDOM->loadXML($xml);
		}
		catch(Exception $e){
			print_r($e);
		}

		$root = $stringDOM->documentElement; //获取XML数据的根

		$children = $root->childNodes; //获得$node的所有子节点

		foreach($children as $e) //循环读取每一个子节点
		{
			if($e->nodeType == XML_ELEMENT_NODE) //如果子节点为节点对象，则调用函数处理
			{
				$value= iconv("UTF-8",$charset,$e->nodeValue); //注意要转码对于中文，因为XML默认为UTF-8格式   
				$hashMap[$e->nodeName] = $value;
			
			}
		}
		return  $hashMap;
	}
	
	
}


?>