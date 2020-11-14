<?php
namespace imcat;
require __DIR__."/config.php";

//计算得出通知验证结果
$verify_result = dmdoCheck();
if($verify_result) {//验证成功
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //请在这里加上商户的业务逻辑程序代码

    //商户订单号
    $out_trade_no = req('out_trade_no');

    //演示交易号
    $trade_no = req('trade_no');

    $total_fee = req('total_fee');

    //交易状态 
    $trade_status = req('trade_status');

    if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
        //判断该笔订单是否在商户网站中已经做过处理
    }else{
        //echo "trade_status=".req('trade_status');
    }
        
    $msg = lang('a3rd.ureturn_ok');

    $kar = glbDBExt::dbAutID('plus_paylog');
    $rlog = ['kid'=>$kar[0], 'ordid'=>$out_trade_no, 'apino'=>$trade_no, 'amount'=>$total_fee, 'api'=>'Demopay',
        'ufrom'=>'', 'uto'=>'', 'stat'=>'success', 'atime'=>time()]; 
    // kid  ordid   apino   amount  api stat    ufrom   uto expar
    db()->table('plus_paylog')->data($rlog )->insert();

    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    $msg = lang('a3rd.ureturn_ng');
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
$res['api'] = 'Demopay';
$res['stamp'] = date('Y-m-d H:i:s',req('notify_time','0'));
require dirname(__DIR__)."/paydemo_dir/xresult.php";
