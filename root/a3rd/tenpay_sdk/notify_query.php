<?php
//---------------------------------------------------------
//财付通APP接收到财付通的支付成功通知后，通过此接口查询订单的详细情况，
//以确保通知是从财付通发起的，没有被篡改过。
//---------------------------------------------------------

require dirname(__FILE__)."/tenpay_config.php";
require DIR_VENDOR."/a3rd/tenpay_class/NotifyQueryRequest.class.php";

/* 初始化通知验证请求:财付通APP接收到财付通的支付成功通知后，通过此接口查询订单的详细情况，以确保通知是从财付通发起的，没有被篡改过。 */
// 设置在沙箱中运行:正式环境请设置为false
$noqHandler = new NotifyQueryRequest($key);

// 设置在沙箱中运行，正式环境请设置为false,true
$noqHandler->setInSandBox($sandbox);
//----------------------------------------
//以下请求业务参数名称参考开放平台sdk文档-PHP
//----------------------------------------
// 设置财付通App-id: 财付通App注册时，由财付通分配
$noqHandler->setAppid($appid);    

// 设置通知id:支付结果通知id，支付成功返回通知id，要获取订单详细情况需用此ID调用通知验证接口。
$noqHandler->setParameter("notify_id", "GamplMcX9Zl0E6shwd8p5c548DHnJWh7ZKkwCocL40j3Qwj6QkJZiOq5H-Ll2tYNRmP2K-NUga4=");          
// ************************************end*******************************

// 发送请求，并获取返回对象
$Response = $noqHandler->send();

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
}else {// 未正常支付，或者调用异常，如调用超时、网络异常
    echo "支付状态说明:" . $Response->getPayInfo() . "<br/>";
}

?>
