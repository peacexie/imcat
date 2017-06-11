<?php
require dirname(dirname(__FILE__))."/lib/WxPay.Config.php";
error_reporting(E_ERROR); 
require DIR_STATIC.'/ximp/class/QRcodeBase.cls_php';
$data = $_GET["data"];
$url = urldecode($data);
QRcode::png($url);
//$data = 'weixin://wxpay/bizpayurl?sign=XXXXX&appid=XXXXX&mch_id=XXXXX&product_id=XXXXXX&time_stamp=XXXXXX&nonce_str=XXXXX'; 
//$data = 'weixin://wxpay/bizpayurl?appid=wx2421b1c4370ec43b&mch_id=10000100&nonce_str=f6808210402125e30663234f94c87a8c&product_id=1&time_stamp=1415949957&sign=512F68131DD251DA4A45DA79CC7EFE9D';
