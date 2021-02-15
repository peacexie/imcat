<?php    

    /*
     * 为应用开启回调模式时需要调用此脚本进行服务器真实性验证
     */

    require_once "../lib/msgcrypt.php";
    require_once "../lib/helper.php";
    
    //读取配置
    $appConfigs = loadConfig();
    $config = getConfigByAgentId(1000002);  //此处替换为你需要测试的应用ID ！！！

    $token  = $config['Token'];    
    $corpId = $appConfigs['CorpId'];
    $encodingAesKey = $config['EncodingAESKey'];

    /*
     * 企业开启回调模式时，企业微信后台会向验证url发送一个get请求
     * 此逻辑需要先开通回调模式并将代码部署到服务器后进行验证
    */

    $sVerifyMsgSig = urldecode($_GET["msg_signature"]);
    $sVerifyTimeStamp = urldecode($_GET["timestamp"]);
    $sVerifyNonce = urldecode($_GET["nonce"]);
    $sVerifyEchoStr = urldecode($_GET["echostr"]);

    // 需要返回的明文
    $sEchoStr = "";

    $wxcpt = new MsgCrypt($token, $encodingAesKey, $corpId);
    $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);

    if ($errCode == 0) {
        // 验证URL成功，将sEchoStr返回
        echo $sEchoStr;
        exit(0);
    } else {
        print("ERR: " . $errCode . "\n\n");
    }
?>
