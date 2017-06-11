<?php
require dirname(__FILE__)."/alipay.config.php";
require LIBS_PAYRUN."/alipay_notify.class.php";

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();

if($verify_result) {//验证成功
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //请在这里加上商户的业务逻辑程序代码
    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

    //商户订单号
    $out_trade_no = $_GET['out_trade_no'];

    //支付宝交易号
    $trade_no = $_GET['trade_no'];

    //交易状态
    $trade_status = $_GET['trade_status'];

    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
        //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
    }
    else {
      //echo "trade_status=".$_GET['trade_status'];
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
    $res[$k1] = req($k2);  
}
$res['msg'] = $msg;
$res['api'] = 'AliWapdir';
$res['stamp'] = req('notify_time');
require dirname(dirname(__FILE__))."/paydemo_dir/xresult.php";
