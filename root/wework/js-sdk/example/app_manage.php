<?php

	/*
	 * 自定义应用的基本接口测试
	 * 注意：创建应用菜单以及获取应用菜单需要开启应用的回调模式
	 */

	require_once "../lib/app_api.php";	

	$agentId = 1000002;  //替换为你需要测试的应用ID
	$api = new APP_API($agentId);

	function testQueryApp($instance){			
		print($instance->queryAppInfo());		
	}

	function testUpdateApp($instance,$agentId){

		$info = array();
		$info["agentid"] = $agentId;
		$info["report_location_flag"] = 0;		
		$info["name"] = "HR_助手";
		$info["description"] = "企业助手";
		$info["redirect_domain"] = "open.work.weixin.qq.com";
		$info["isreportenter"] = 0;
		$info["home_url"] = "http://www.qq.com";
			
		//$info["logo_mediaid"] = "";  //需要传入合法的mediaid，否则会返回异常，可调用媒体上传接口先做LOGO上传操作
		
		print($instance->updateAppInfo($info));
	}

	//test entry	
	$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : "query";

	switch ($cmd) {
		case 'query':		
			testQueryApp($api);
			break;
		case 'update':
			testUpdateApp($api,$agentId);
			break;	
		default:			
			break;
	}
?>

