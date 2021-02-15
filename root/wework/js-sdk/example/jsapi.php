<?php
  require_once "../lib/jssdk.php";  

  //第一步: 在服务端生成签名
  $jssdk = new JSSDK(1000002);
  $signPackage = $jssdk->GetSignPackage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JSAPI 签名输出测试</title>    
    <style type="text/css">
      span{
          color:green;
      }
    </style>
</head>
<body>
  <p>appId：<span><?php echo $signPackage["appId"];?></span></p>
  <p>timestamp：<span><?php echo $signPackage["timestamp"];?></span></p>
  <p>nonceStr：<span><?php echo $signPackage["nonceStr"];?></span></p>
  <p>signature：<span><?php echo $signPackage["signature"];?></span></p>
</body>

<!-- 第二步: 引用JSAPI 的脚本文件 -->
<script src="https://res.wx.qq.com/wwopen/js/jsapi/jweixin-1.0.0.js"></script>
<script>
  /*
   * 第三步: 配置jsapi的权限 
   * 注意：所有的JS接口只能可信域名下调用   
   */
  wx.config({
      debug: true,
      appId: '<?php echo $signPackage["appId"];?>',    //此处的appId等同于企业的CorpID
      timestamp: <?php echo $signPackage["timestamp"];?>,
      nonceStr: '<?php echo $signPackage["nonceStr"];?>',
      signature: '<?php echo $signPackage["signature"];?>',
      jsApiList: [
        // 所有要调用的 API 都要加到这个列表中
      ]
  });
  wx.ready(function () {
    //TODO： 执行和jsapi相关的初始化操作
  });
</script>
</html>
