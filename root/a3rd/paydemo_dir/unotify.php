<?php
require_once(dirname(__FILE__)."/config.php");

//计算得出通知验证结果
$verify_result = dmdoCheck();

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代

	//商户订单号
	$out_trade_no = basReq::val('out_trade_no');

	//演示交易号
	$trade_no = basReq::val('trade_no');

	//交易状态
	$trade_status = basReq::val('trade_status');

    if($trade_status == 'TRADE_FINISHED') {
		//判断该笔订单是否在商户网站中已经做过处理
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }elseif($trade_status == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
	echo "success";		//请不要修改或删除
	exvOpay::notifyDemopay('success');
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    echo "fail";
	exvOpay::notifyDemopay('fail');

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
