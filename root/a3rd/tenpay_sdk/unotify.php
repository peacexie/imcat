<?php

//---------------------------------------------------------
//支付成功回调接收,财付通后台调用此地址
//---------------------------------------------------------

require_once(dirname(__FILE__)."/tenpay_config.php");
require_once(DIR_VENDOR."/a3rd/tenpay_class/PayResponse.class.php");
require_once(DIR_VENDOR."/a3rd/tenpay_class/NotifyQueryRequest.class.php");

/* 创建支付应答对象 */
$resHandler = new PayResponse($config['key']);

//获取通知id:支付结果通知id，支付成功返回通知id，要获取订单详细情况需用此ID调用通知验证接口。
$notifyId = $resHandler->getNotifyId();

$resHandler->acknowledgeSuccess();
$status = 'success';

/* 初始化通知验证请求:财付通APP接收到财付通的支付成功通知后，通过此接口查询订单的详细情况，以确保通知是从财付通发起的，没有被篡改过。 */
// 设置在沙箱中运行:正式环境请设置为false
$noqHandler = new NotifyQueryRequest($key);

// 设置在沙箱中运行，正式环境请设置为false
$noqHandler->setInSandBox($sandbox);
//----------------------------------------
//以下请求业务参数名称参考开放平台sdk文档-PHP
//----------------------------------------
// 设置财付通App-id: 财付通App注册时，由财付通分配
$noqHandler->setAppid($appid);

// 设置通知id:支付结果通知id，支付成功返回通知id，要获取订单详细情况需用此ID调用通知验证接口。
$noqHandler->setParameter("notify_id", $notifyId);
// ************************************end*******************************

// 发送请求，并获取返回对象
$Response = $noqHandler->send();

// ********************以下返回业务参数名称参考开放平台sdk文档-PHP*************************
if( $Response->isPayed()){    
	 $status = 'success';
	 exvOpay::notifyTenpay('success');
}else{
	$status = 'fail';
	exvOpay::notifyTenpay('fail');
}

exit($status);

/*
// 创建支付结果反馈响应对象：支付跳转接口为异步返回，用户在财付通完成支付后，财付通通过回调return_url和notify_url向财付通APP反馈支付结果。
$resHandler = new PayResponse($key);

//获取通知id:支付结果通知id，支付成功返回通知id，要获取订单详细情况需用此ID调用通知验证接口。
$notifyId = $resHandler->getNotifyId(); 

// 告知财付通通知发送成功，如不加上下行代码会导致财付通不停里通知财付通app，即不停里调用财付通app的notify_url进行通知
$resHandler->acknowledgeSuccess();
// 开始通知验证，具体方法请参考notify_query.php的介绍

exvOpay::notifyDemopay('success');
*/

?>