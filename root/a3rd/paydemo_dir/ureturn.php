<?php
require_once(dirname(__FILE__)."/config.php");

//计算得出通知验证结果
$verify_result = dmdoCheck();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码

	//商户订单号
	$out_trade_no = basReq::val('out_trade_no');

	//演示交易号
	$trade_no = basReq::val('trade_no');

	//交易状态
	$trade_status = basReq::val('trade_status');

    if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
    }else{
   		//echo "trade_status=".basReq::val('trade_status');
    }
		
	$msg = "验证成功";

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    $msg = "验证失败";
}

$cfg = array(
	'ordid'=>'out_trade_no',
	'feeamount'=>'total_fee',
	'apino'=>'trade_no',
	'status'=>'trade_status',
);
foreach($cfg as $k1=>$k2){ 
	$res[$k1] = basReq::val($k2);  
}
$res['msg'] = $msg;
$res['api'] = 'Demopay';
$res['stamp'] = date('Y-m-d H:i:s',basReq::val('notify_time','0'));
require_once(dirname(dirname(__FILE__))."/paydemo_dir/xresult.php");
