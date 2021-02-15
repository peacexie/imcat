<?php
  require_once "../lib/jssdk.php";  

  //第一步: 在服务端生成签名
  $jssdk = new JSSDK(1000002);
  $signPackage = $jssdk->getSignPackage();
  $signAgent = $jssdk->getSignPackage(1);
  $thirdNo = date('md_Hi');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JSAPI 选人接口</title>    
    <style type="text/css">
      span{
          color:green;
      }
    </style>
</head>
<body>
  <p onclick="pk2()">
    <b>触发选人</b>
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
      appId: '<?php echo $signPackage["appId"];?>', //此处的appId等同于企业的CorpID
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
        agentid: '1000002', // 必填，企业微信的应用id （e.g. 1000247）
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
            //wewLog('agentConfig:success', res);
            // pk2();
        },
        fail: function(res) {
            //wewLog('agentConfig:fail', res);
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

function pk2(){

    wx.invoke("selectEnterpriseContact", {
        "fromDepartmentId": -1,// 必填，表示打开的通讯录从指定的部门开始展示，-1表示自己所在部门开始, 0表示从最上层开始
        "mode": "multi",// 必填，选择模式，single表示单选，multi表示多选
        "type": ["department", "user"],// 必填，选择限制类型，指定department、user中的一个或者多个
        "selectedDepartmentIds": ["2","3"],// 非必填，已选部门ID列表。用于多次选人时可重入，single模式下请勿填入多个id
        "selectedUserIds": ["lisi","lisi2"]// 非必填，已选用户ID列表。用于多次选人时可重入，single模式下请勿填入多个id
    },function(res){
        if (res.err_msg == "selectEnterpriseContact:ok")
        {
            if(typeof res.result == 'string')
            {
                    res.result = JSON.parse(res.result) //由于目前各个终端尚未完全兼容，需要开发者额外判断result类型以保证在各个终端的兼容性
            }
            var selectedDepartmentList = res.result.departmentList;// 已选的部门列表
            for (var i = 0; i < selectedDepartmentList.length; i++)
            {
                    var department = selectedDepartmentList[i];
                    var departmentId = department.id;// 已选的单个部门ID
                    var departemntName = department.name;// 已选的单个部门名称
            }
            var selectedUserList = res.result.userList; // 已选的成员列表
            for (var i = 0; i < selectedUserList.length; i++)
            {
                    var user = selectedUserList[i]; 
                    var userId = user.id; // 已选的单个成员ID
                    var userName = user.name;// 已选的单个成员名称
                    var userAvatar= user.avatar;// 已选的单个成员头像
                    wewLog('wx.user', userId+'/'+userName);
            }
        }else{
            wewLog('wx.selectEnterpriseContact', res);
        }
    });

}

</script>
</html>
