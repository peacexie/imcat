<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 注册/登录:相关设置

### 注册认证方式
$_sy_user['regchecks'] = array(
    'idpwd' => '传统注册',
    'mail-act' => '邮件认证', // 发邮件-连接激活
    'sms-vcode' => '短信验证码', // 发短信-验证码
    // (暂不用,好像没看过以下这种方式)
    //'mail-vcode' => '邮件验证码', // 发验证码到邮件
    //'sms-act' => '短信认证', // 发短信验证码-认证激活
);
$_sy_user['regdebug'] = '1';
$_sy_user['regnow'] = 'idpwd'; // 当前使用方式

### 登录类型方式
$_sy_user['logintypes'] = array(
    'idpwd' => array(
        'title' => '账号密码',
        'open' => 1,
        'def' => 1,
    ),
    'qqcon' => array(
        'title' => 'QQ登录',
        'open' => 1,
        'def' => 0,
    ),
    'weixin' => array(
        'title' => '微信登录',
        'open' => 1,
        'def' => 0,
    ),
);

$signmsg = '【某某网】';
$_sy_user['utpls'] = array(
    //'idpwd' => '',
    'mail-act' => "umc:uio/mail-regact", // 注册激活邮件:模板地址
    'sms-vcode' => "您的注册验证码是：{code} $signmsg", 
    //'mail-vcode' => 'umc:uio/reg-xxx.html', 
    //'sms-act' => '您的激活验证码是：{code} $signmsg', 
    'mail-getpw' => "umc:uio/mail-getpw", // 找回密码邮件:模板地址

);

### 配置说明

/*

* 配置说明 

 - 这里只设置相关`注册/登录`开关, 具体第三方帐号,appkey等,请按如下提示去配置
 - 发邮件配置：/root/cfgs/excfg/ex_mail.php
 - 发短信配置：/root/cfgs/excfg/ex_sms.php
 - QQ登录配置：/root/cfgs/excfg/ex_a3rd.php 中 `qqconn` 区块
 - 微信登录配置：后台 >> 架构 >> xxx >> 配置 `admin(总站)` 对应的微信公众号(需要服务号)配置

* 关于发短信-验证码
 - 为防止滥发信息，短信供应商普遍要求：发短信验证码时先用一个图片验证码验证；
 - 最后结果是：先输入图片验证码，再发(收)短信验证码，最后输入短信验证码……

*/

