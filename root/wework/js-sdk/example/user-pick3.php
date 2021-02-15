<?php
  require_once "../lib/jssdk.php";  

  //第一步: 在服务端生成签名
  $jssdk = new JSSDK(1000002);
  $signPackage = $jssdk->getSignPackage();
  $signAgent = $jssdk->getSignPackage(1);

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
    <script src="<?=PATH_VIEWS?>/wework/assets/exfunc.js"></script>
</head>
<body>
  <p onclick="pickOne()">
    <b>触发选人</b>
    <input class="weui-input" name="fm[douname]" value="" id='fm[douname]' placeholder="处理人默认为自己">
    <input class="weui-input" name="fm[douid]" value="" id='fm[douid]' placeholder="处理人默认为自己">
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

wx.config({
    //debug: true,
    beta: true,
    appId: '<?php echo $signPackage["appId"];?>', //此处的appId等同于企业的CorpID
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    /*success: function(res) {
        wewLog('config:success:', res);
    },*/
    jsApiList: jsApiListDef
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
});

wx.error(function (res) {
    alert(JSON.stringify(res));
    wewLog('wx.error', res);
});

function pickOne(){
    wx.invoke("selectEnterpriseContact", {
        "fromDepartmentId": -1,// 必填，表示打开的通讯录从指定的部门开始展示，-1表示自己所在部门开始, 0表示从最上层开始
        "mode": "single",// 必填，选择模式, single=表示单选，multi=表示多选
        "type": ["user"],// 必填，选择限制类型，指定[department,user]中的一个或者多个
    },function(res){
        if (res.err_msg == "selectEnterpriseContact:ok")
        {
            if(typeof res.result == 'string'){
                res.result = JSON.parse(res.result) //由于目前各个终端尚未完全兼容，需要开发者额外判断result类型以保证在各个终端的兼容性
            }
            var selectedUserList = res.result.userList; // 已选的成员列表
            for (var i = 0; i < selectedUserList.length; i++)
            {
                var user = selectedUserList[i]; 
                eid('fm[douname]').value = user.name;
                eid('fm[douid]').value = user.id;
                log(user);
                return;
            }
        }else{
            log(res);
        }
    });

}

</script>
</html>
