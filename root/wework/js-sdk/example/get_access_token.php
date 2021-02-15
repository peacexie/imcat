<?php
	
	/**
	 * 测试access_token的生成接口
	 *
	 *
	 * 注意：
	 * 在企业微信里面，每一个应用都有一个独立的access_token，包括系统的通讯录接口，所以在使用的时候需要区分获取并存储，否则容易出现混淆！
	 * 
	 */

	require_once "../lib/access_token.php";
	
	$txl_ins = new AccessToken("txl");
	$hr_ins  = new AccessToken(1000002);  //输入自定义应用ID

	print("获取通讯录接口的access_token：<br/>".$txl_ins->getAccessToken());	
	print("<br/><br/>");
	print("获取自定义应用的access_token：<br/>".$hr_ins->getAccessToken());
?>

