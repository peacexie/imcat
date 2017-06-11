<?php

//---------------------------------------------------------
//财付通即时到帐支付应答（处理回调）示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require dirname(__FILE__)."/tenpay_config.php";
require DIR_VENDOR."/a3rd/tenpay_class/PayResponse.class.php";

/* 创建支付结果反馈响应对象：支付跳转接口为异步返回，用户在财付通完成支付后，财付通通过回调return_url和notify_url向财付通APP反馈支付结果。 */
$resHandler = new PayResponse($key);

//获取通知id:支付结果通知id，支付成功返回通知id，要获取订单详细情况需用此ID调用通知验证接口。
echo "<br/>" . "本次支付的通知ID：" . $resHandler->getNotifyId() . "<br/>";

?>