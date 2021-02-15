<?php
  require_once "../lib/jssdk.php";  

  //第一步: 在服务端生成签名
  $jssdk = new JSSDK(1000019); // 1000002,1000019
  $signPackage = $jssdk->getSignPackage();
  $signAgent = $jssdk->getSignPackage(1);
  $thirdNo = date('md_Hi');
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
  <p>
    <b>提示：拉起审批
  </p>

</body>

<!-- 
第二步: 引用JSAPI 的脚本文件 
https://res.wx.qq.com/wwopen/js/jsapi/jweixin-1.0.0.js
http://res.wx.qq.com/open/js/jweixin-1.2.0.js
-->
<script src="https://res.wx.qq.com/wwopen/js/jsapi/jweixin-1.0.0.js"></script>
<script>
  /*
   * 第三步: 配置jsapi的权限 
   * 注意：所有的JS接口只能可信域名下调用   
   */

  var jsApiList = [
        'checkJsApi',
        'onMenuShareAppMessage',
        'onMenuShareWechat',
        'onMenuShareTimeline',
        'shareAppMessage',
        'shareWechatMessage',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'closeWindow',
        'scanQRCode',
        'previewFile',
        'openEnterpriseChat',
        'selectEnterpriseContact',
        'onHistoryBack',
        'openDefaultBrowser'
        // 所有要调用的 API 都要加到这个列表中
  ];

  function wewLog(key, res){
      alert(key+':::'+JSON.stringify(res));
      console.log(res); // 回调
  }

  wx.config({
      debug: true,
      beta: true,
      appId: '<?php echo $signPackage["appId"];?>',    //此处的appId等同于企业的CorpID
      timestamp: <?php echo $signPackage["timestamp"];?>,
      nonceStr: '<?php echo $signPackage["nonceStr"];?>',
      signature: '<?php echo $signPackage["signature"];?>',
      /*success: function(res) {
          wewLog('config:success:', res);
      },*/
      jsApiList: jsApiList
  }); 


wx.ready(function () { 

    wx.agentConfig({
        corpid: '<?php echo $signAgent["appId"];?>', // 必填，企业微信的corpid，必须与当前登录的企业一致
        agentid: '1000019', // 必填，企业微信的应用id （e.g. 1000247）
        timestamp: <?php echo $signAgent["timestamp"];?>, // 必填，生成签名的时间戳
        nonceStr: '<?php echo $signAgent["nonceStr"];?>', // 必填，生成签名的随机串
        signature: '<?php echo $signAgent["signature"];?>', // 必填，签名，见附录1
        jsApiList: [
            'selectExternalContact',
            'openUserProfile',
            'thirdPartyOpenPage',
            'getCurExternalContact',
        ], //必填
        success: function(res) {
            wewLog('agentConfig:success', res);

            wx.invoke('thirdPartyOpenPage', {
                "oaType": "10001",// 10001,String:10001-发起审批；10002-查看审批详情。
                "templateId": "9de288d16634b9fa1c043d741b3d337f_913900360",// String
                "thirdNo": "thirdNo"+'<?=$thirdNo?>',// String
                "extData": {
                    'fieldList': [{
                        'title': '售后a<?=$thirdNo?>',
                        'type': 'text',
                        'value': '投影仪维修(测试不要理会)',
                    },
                    {
                        'title': '配件采购',
                        'type': 'text',
                        'value': '8899.00元',
                    },
                    {
                        'title': '申请时间',
                        'type': 'text',
                        'value': '<?=date('Y-m-d H:i')?>',
                    },
                    {
                        'title': '参考链接',
                        'type': 'link',
                        'value': 'https://open.work.weixin.qq.com/devtool/query',
                    },
                    /*{
                        'title': '详情',
                        'type': 'text',
                        'value': '这里写好多字…<b>WPS</b> <a href="https://weui.io/images/logo.png">Office</a>是一款老牌的办公软件套装,可以实现办公软件最常用的文字、表格、演示等多种功能。',
                    },*/
                    {
                        'title': '备注',
                        'type': 'text',
                        'value': '第一领导审批完采购即可买东西；采购审批完表示已可领取东西，系统自动交给原售后单处理人',
                    }],}
                },
                function(res) {
                    wewLog('invoke:thirdPartyOpenPage', res);
                }
            );

        },
        fail: function(res) {
            wewLog('agentConfig:fail', res);
            if(res.errMsg.indexOf('function not exist') > -1){
                alert('版本过低请升级')
            }
        }
    }); 

    //TODO： 执行和jsapi相关的初始化操作
});

wx.error(function (res) {
    wewLog('wx.error', res);
});


</script>
</html>
