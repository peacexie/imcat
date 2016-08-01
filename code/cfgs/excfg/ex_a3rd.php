<?php

/*

'合作身份者ID，以2088开头的16位纯数字
partner         = "2088802931044344"

'安全检验码，以数字和字母组成的32位字符
key   			= "gunm2vfn3wtr7hhb66mm5easwguj5y8t"

dianyuanyuan0109@163.com
13790321373@163.com

sid_AliPay 58722687@qq.com
sid_PayPal elifebike@hotmail.com

*/

### alipay: =======================================================================

$pay_uinfo['demo']['partner']  = '2088012340123401234';
//合作身份者id，以2088开头的16位纯数字
$pay_uinfo['demo']['email']	  = 'demo@domain.com';
//收款支付宝账号
$pay_uinfo['demo']['key']      = '0123401234012340123401234';
//安全检验码，以数字和字母组成的32位字符
$pay_uinfo['demo']['sandbox']  = false;  
//设置在沙箱中运行，正式环境请设置为false,true

$pay_uinfo['ali']['partner']  = '2088311817647422';                 // 2088002033470355,                 2088311817647422
//合作身份者id，以2088开头的16位纯数字:                
$pay_uinfo['ali']['email']	  = '910377558@qq.com';         // fangyuwei2003@aliyun.com          910377558@qq.com
//收款支付宝账号:                                        
$pay_uinfo['ali']['key']      = 'jp4yhwpr7op6kexac3g623e8qshby59g'; // xt7oyie9cc50uo407j1h6y8x9kruavkf, jp4yhwpr7op6kexac3g623e8qshby59g
//安全检验码，以数字和字母组成的32位字符:             
$pay_uinfo['ali']['sandbox']  = false;  
//设置在沙箱中运行，正式环境请设置为false,true

### tenpay: =======================================================================

$pay_uinfo['ten']['appid']   = "1202397201";
//设置财付通App-id: 财付通App注册时，由财付通分配   
$pay_uinfo['ten']['key']     = "cdb73ab93e390d4e9925f56e919d2618"; 
//签名密钥: 开发者注册时，由财付通分配 
$pay_uinfo['ten']['sandbox'] = false;  
//设置在沙箱中运行，正式环境请设置为false,true

### paypal: =======================================================================

$pay_uinfo['pal']['user']    = "seller_username_here"; 
$pay_uinfo['pal']['pass']    = "seller_password_here"; 
$pay_uinfo['pal']['sign']    = "seller_signature_here"; 
$pay_uinfo['pal']['sandbox'] = false;  
//设置在沙箱中运行，正式环境请设置为false,true

### qqconn: =======================================================================

$pay_uinfo['qqconn']['appid']    = "yourappid"; 
$pay_uinfo['qqconn']['appkey']    = "yourappkey";
$pay_uinfo['qqconn']['callback'] = "http://your domain/oauth/get_access_token.php";  

### testapi: =======================================================================

$pay_uinfo['test']['user']    = ""; 
$pay_uinfo['test']['pass']    = "";
$pay_uinfo['test']['sandbox'] = false;  

