<?php

### alipay: =======================================================================

$_cfgs['demo']['partner']  = '2088012340123401234';
//合作身份者id，以2088开头的16位纯数字
$_cfgs['demo']['email']      = 'demo@domain.com';
//收款支付宝账号
$_cfgs['demo']['key']      = '0123401234012340123401234';
//安全检验码，以数字和字母组成的32位字符
$_cfgs['demo']['sandbox']  = false;  
//设置在沙箱中运行，正式环境请设置为false,true

$_cfgs['ali']['partner']  = '2088xxxxxxxxxxxx';
//合作身份者id，以2088开头的16位纯数字
$_cfgs['ali']['email']      = 'email-userid@aliyun.com';
//收款支付宝账号
$_cfgs['ali']['key']      = 'keyid-keyid-keyid-keyid-keyid';
//安全检验码，以数字和字母组成的32位字符
$_cfgs['ali']['sandbox']  = false;  
//设置在沙箱中运行，正式环境请设置为false,true

# 提示：确认或复制文件 'cacert.pem' 到: /vendor/a3rd/alipay_class/

### tenpay: =======================================================================

$_cfgs['ten']['appid']   = "1202397201"; //0000000202,1202397201
//设置财付通App-id: 财付通App注册时，由财付通分配   
$_cfgs['ten']['key']     = "cdb73ab93e390d4e9925f56e919d2618"; //33182060802321342830012253725443,cdb73ab93e390d4e9925f56e919d2618,
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

$_cfgs['qqconn']['appid']    = "yourappid"; 
$_cfgs['qqconn']['appkey']    = "yourappkey";
$_cfgs['qqconn']['callback'] = "http://your domain/oauth/get_access_token.php";
$_cfgs['qqconn']['scope']       = 'get_user_info';
$_cfgs['qqconn']['errorReport'] = true;
$_cfgs['qqconn']['storageType'] = 'file';

### testapi: =======================================================================

$_cfgs['test']['user']    = ""; 
$_cfgs['test']['pass']    = "";
$_cfgs['test']['sandbox'] = false;  

$_ex_a3rd = $_cfgs;
