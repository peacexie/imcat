<?php
global $_cbase;

### alipay: =======================================================================

$_cfgs['demo']['partner']  = '2088012340123401234';
//合作身份者id，以2088开头的16位纯数字
$_cfgs['demo']['email']      = 'demo@domain.com';
//收款支付宝账号
$_cfgs['demo']['key']      = '0123401234012340123401234';
//安全检验码，以数字和字母组成的32位字符
$_cfgs['demo']['sandbox']  = false;  
//设置在沙箱中运行，正式环境请设置为false,true

$_cfgs['ali']['partner']  = '2088311817647422';                 // 2088002033470355,                 2088311817647422
//合作身份者id，以2088开头的16位纯数字:                
$_cfgs['ali']['email']      = '910377558@qq.com';         // fangyuwei2003@aliyun.com          910377558@qq.com
//收款支付宝账号:                                        
$_cfgs['ali']['key']      = 'jp4yhwpr7op6kexac3g623e8qshby59g'; // xt7oyie9cc50uo407j1h6y8x9kruavkf, jp4yhwpr7op6kexac3g623e8qshby59g
//安全检验码，以数字和字母组成的32位字符:             
$_cfgs['ali']['sandbox']  = false;  
//设置在沙箱中运行，正式环境请设置为false,true

# 提示：确认或复制文件 'cacert.pem' 到: /vendor/a3rd/alipay_class/

### tenpay: =======================================================================

$_cfgs['ten']['appid']   = "1202397201";
//设置财付通App-id: 财付通App注册时，由财付通分配   
$_cfgs['ten']['key']     = "cdb73ab93e390d4e9925f56e919d2618"; 
//签名密钥: 开发者注册时，由财付通分配 
$_cfgs['ten']['sandbox'] = false;  
//设置在沙箱中运行，正式环境请设置为false,true

### paypal: =======================================================================

$_cfgs['pal']['user']    = "seller_username_here"; 
$_cfgs['pal']['pass']    = "seller_password_here"; 
$_cfgs['pal']['sign']    = "seller_signature_here"; 
$_cfgs['pal']['sandbox'] = false;  
//设置在沙箱中运行，正式环境请设置为false,true

### qqconn: =======================================================================

$_cfgs['qqconn']['appid']       = '101403529'; 
// 100330156,222222
$_cfgs['qqconn']['appkey']      = "27fc2b8074536aaf6ef74be359400d08"; 
// e184b2f2d2a12bc8f24cc551b6e80bff,005831692a444765a0db25a4a5ac052c
$_cfgs['qqconn']['callback']    = $_cbase['run']['roots'].'/a3rd/qqconn/cback.php';
$_cfgs['qqconn']['scope']       = 'get_user_info';
$_cfgs['qqconn']['errorReport'] = true;
$_cfgs['qqconn']['storageType'] = 'file';
/*
$_cfgs['qqconn']['host']        = 'localhost';
$_cfgs['qqconn']['user']        = 'root';
$_cfgs['qqconn']['password']    = 'root';
$_cfgs['qqconn']['database']    = 'test';
*/

### testapi: =======================================================================

$_cfgs['test']['user']    = ""; 
$_cfgs['test']['pass']    = "";
$_cfgs['test']['sandbox'] = false;  

$_ex_a3rd = $_cfgs;
