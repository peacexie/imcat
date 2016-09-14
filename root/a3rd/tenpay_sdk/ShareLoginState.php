<?php
//---------------------------------------------------------
//共享登录接收当前用户信息
//---------------------------------------------------------
require_once(dirname(__FILE__)."/tenpay_config.php");
require_once(DIR_VENDOR."/a3rd/tenpay_class/ShareLoginState.class.php");

/* 创建共享登陆态对象：买家转到财付通APP中时，系统将买家的ID传入APP，方便用户订单生成、用户状态更新等相关操作。 */
$shareLogin = new ShareLoginState($key);

// 获取用户id
echo $shareLogin->getUserId();

?>