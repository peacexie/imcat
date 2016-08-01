<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta charset="utf-8">
	<title>支付宝纯担保交易接口接口</title>
    <style type="text/css">.tc { text-align:center; }</style>
</head>
<?php

require_once(dirname(__FILE__)."/alipay.config.php");
require_once(LIBS_PAYRUN."/alipay_submit.class.php");

/**************************请求参数**************************/

        //支付类型
        $payment_type = "1";
        //必填，不能修改
		
        //服务器异步通知页面路径
        $notify_url = $_cbase['run']['roots']."/a3rd/alipay_wscow/unotify_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = $_cbase['run']['roots']."/a3rd/alipay_wscow/ureturn_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no = $_POST['ordid'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $_POST['title'];
        //必填

        //付款金额
        $price = $_POST['feetotle'];
        //必填

        //商品数量
        $quantity = "1";
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
        //物流费用
        $logistics_fee = "0.00";
        //必填，即运费
        //物流类型
        $logistics_type = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "SELLER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述

        $body = $_POST['ordbody'];
        //商品展示地址
        $show_url = $_POST['showurl'];
        //需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html

        //收货人姓名
        $receive_name = $_POST['receive_name'];
        //如：张三

        //收货人地址
        $receive_address = $_POST['receive_address'];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号

        //收货人邮编
        $receive_zip = $_POST['receive_zip'];
        //如：123456

        //收货人电话号码
        $receive_phone = $_POST['receive_phone'];
        //如：0571-88158090

        //收货人手机号码
        $receive_mobile = $_POST['receive_mobile'];
        //如：13312341234

/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "create_partner_trade_by_buyer",
		"partner" => trim($alipay_config['partner']),
		"seller_email" => trim($alipay_config['seller_email']),
		"payment_type"	=> $payment_type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"price"	=> $price,
		"quantity"	=> $quantity,
		"logistics_fee"	=> $logistics_fee,
		"logistics_type"	=> $logistics_type,
		"logistics_payment"	=> $logistics_payment,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"receive_name"	=> $receive_name,
		"receive_address"	=> $receive_address,
		"receive_zip"	=> $receive_zip,
		"receive_phone"	=> $receive_phone,
		"receive_mobile"	=> $receive_mobile,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo "<p class='tc'>$html_text</p>";

?>
</body>
</html>