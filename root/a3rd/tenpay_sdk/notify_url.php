<?php

//---------------------------------------------------------
//支付成功回调接收,财付通后台调用此地址
//---------------------------------------------------------

require dirname(__FILE__)."/tenpay_config.php";
require DIR_VENDOR."/a3rd/tenpay_class/PayResponse.class.php";

/* 创建支付结果反馈响应对象：支付跳转接口为异步返回，用户在财付通完成支付后，财付通通过回调return_url和notify_url向财付通APP反馈支付结果。 */
$resHandler = new PayResponse($key);
//获取通知id:支付结果通知id，支付成功返回通知id，要获取订单详细情况需用此ID调用通知验证接口。
echo $resHandler->getNotifyId();

// 告知财付通通知发送成功，如不加上下行代码会导致财付通不停里通知财付通app，即不停里调用财付通app的notify_url进行通知
$resHandler->acknowledgeSuccess();
// 开始通知验证，具体方法请参考notify_query.php的介绍


?>