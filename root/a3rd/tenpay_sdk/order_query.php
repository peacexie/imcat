<?php
//---------------------------------------------------------
//根据商户订单号或者财付通订单号查询财付通侧记录的具体订单信息
//---------------------------------------------------------

require_once(dirname(__FILE__)."/tenpay_config.php");
require_once(DIR_VENDOR."/a3rd/tenpay_class/PayRequest.class.php"); 
require_once(DIR_VENDOR."/a3rd/tenpay_class/OrderQueryRequest.class.php"); 


// 初始化订单查询请求:根据商户订单号或者财付通订单号查询财付通侧记录的具体订单信息.
$ordHandler = new OrderQueryRequest($key);

// 设置在沙箱中运行，正式环境请设置为false
$ordHandler->setInSandBox($sandbox);

//----------------------------------------
//以下请求业务参数名称参考开放平台sdk文档-PHP
//----------------------------------------

// 设置财付通App-id: 财付通App注册时，由财付通分配
$ordHandler->setAppid($appid);		

// 设置财付通App订单号:财付通APP的订单号
$ordHandler->setParameter("out_trade_no", "test100000001");    
// **********************end*************************

// 发送请求，并获取返回对象
$Response = $ordHandler->send();
// ********************以下返回业务参数名称参考开放平台sdk文档-PHP*************************
if($Response->isPayed()) {// 已经支付
	// 已经支付财付通app订单号
	echo "支付成功，应用订单号：" . $Response->getParameter("out_trade_no") . "<br/>";
	// 财付通app订单号对应的财付通订单号
	echo "财付通订单号:" . $Response->getParameter("transaction_id") . "<br/>";
	// 支付金额，单位：分
	echo "支付金额:" . $Response->getParameter("total_fee") . "<br/>";
	// 支付完成时间,格式为yyyymmddhhmmss,如20091227091010
	echo "支付完成时间:" . $Response->getParameter("time_end") . "<br/>";
}else{// 未正常支付，或者调用异常，如调用超时、网络异常
    echo "支付状态说明:" . $Response->getPayInfo() . "<br/>";
}


?>
