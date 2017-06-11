<?php
require dirname(__FILE__)."/config.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php lang('a3rd.uapi_title',0); ?></title>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php?act=autoJQ"></script>
<style type="text/css">.tc { text-align:center; }</style>
</head>
<?php
//logResult('test');
/**************************请求参数**************************/

//支付类型
$payment_type = "1"; 
//必填，不能修改

//服务器异步通知页面路径
$notify_url = $_cbase['run']['roots']."/a3rd/paydemo_dir/unotify.php";
//需http://格式的完整路径，不能加?id=123这类自定义参数
//页面跳转同步通知页面路径
$return_url = $_cbase['run']['roots']."/a3rd/paydemo_dir/ureturn.php";
//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

//商户订单号
$out_trade_no = req('out_trade_no');
//商户网站订单系统中唯一订单号，必填

//订单名称
$subject = req('subject');
//必填

//付款金额
$total_fee = req('total_fee');
//必填

//订单描述
$body = req('ordbody');

//商品展示地址
$show_url = req('showurl','','Safe4');
//需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
        "service" => "create_direct_pay_by_user",
        "partner" => trim($demopay_config['partner']),
        "seller_email" => trim($demopay_config['seller_email']),
        "payment_type"    => $payment_type,
        "notify_url"    => $notify_url,
        "return_url"    => $return_url,
        "out_trade_no"    => $out_trade_no,
        "subject"    => $subject,
        "total_fee"    => $total_fee,
        "body"    => $body,
        "show_url"    => $show_url,
        "_input_charset"    => trim(strtolower($demopay_config['input_charset']))
);

?>
<form id='fmopay' name='fmopay' method="post" action="<?php echo PATH_ROOT; ?>/a3rd/paydemo_dir/payweb.php" target="_self">
<?php 
foreach($parameter as $k=>$v){ 
    echo "<input name='{$k}' type='hidden' value='{$v}'>"; 
} 
?>
<p class="tc"><input name="" value="<?php lang('a3rd.uapi_confirm',0); ?>" type="submit" /></p>
</form>
<script>
function opaysubmit(){
    $('#fmopay').submit();
}
setTimeout('opaysubmit()',500);
</script>

</body>
</html>