<?php
require_once __DIR__.'/example/WxPay.Config.php';


function nativeUrl($body, $fee, $ordid, $tag=''){
    global $_cbase;
    $root_url = (\imcat\basEnv::isHttps() ? 'https:' : 'http:') . $_cbase['run']['roots'];
    $logHandler= new CLogFileHandler(__DIR__."/logs/".date('Y-m-d').'.log'); // 初始化日志
    $log = Log::Init($logHandler, 15); 
    $notify = new \NativePay();
    $notify_url = $root_url."/a3rd/wepv3/example/native_notify.php";
    $kstr = "$fee.$ordid.".time();
    $kenc = \imcat\comConvert::sysEncode($kstr);
    $attach = "$kstr@$kenc";
    // order-info
    $input = new WxPayUnifiedOrder();
    $input->SetBody($body); // (x)支付单简要描述
    $input->SetAttach($attach); // 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
    $input->SetOut_trade_no($ordid); // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
    $input->SetTotal_fee($fee*100);
    $input->SetTime_start(date("YmdHis")); //echo date("YmdHis");
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $tag && $input->SetGoods_tag($tag); // 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
    $input->SetNotify_url($notify_url);
    $input->SetTrade_type("NATIVE");
    $input->SetProduct_id("ord_$ordid"); // (x)设置商品ID
    // result,return
    $result = $notify->GetPayUrl($input);
    $url = empty($result["code_url"]) ? 'err_url' : $result["code_url"];
    $enc = str_replace('?', '%3F', $url);
    return ['url'=>$url, 'enc'=>$enc];
}


